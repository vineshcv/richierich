<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CurrentStore;
use App\Services\RazorpayPayments;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private CurrentStore $current,
        private RazorpayPayments $payments,
    ) {
    }

    public function index(Request $request): View
    {
        $storeId = $this->current->adminId();
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'fulfillment' => ['nullable', Rule::in(['none', 'received', 'processing', 'ready', 'shipped'])],
            'payment_method' => ['nullable', Rule::in(['card', 'upi', 'netbanking', 'wallet', 'emi', 'paylater'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'sort' => ['nullable', Rule::in(['created_at', 'customer_name', 'amount', 'status', 'fulfillment_status', 'payment_method'])],
            'dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', Rule::in(['10', '20', '50'])],
        ]);

        $sort = $filters['sort'] ?? 'created_at';
        $dir = $filters['dir'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 20);
        $search = trim((string) ($filters['q'] ?? ''));

        $orders = Order::query()
            ->where('status', 'paid')
            ->when($storeId, fn ($query) => $query->where('store_id', $storeId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('razorpay_order_id', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(($filters['fulfillment'] ?? null) === 'none', fn ($query) => $query->whereNull('fulfillment_status'))
            ->when(in_array($filters['fulfillment'] ?? null, ['received', 'processing', 'ready', 'shipped'], true), fn ($query) => $query->where('fulfillment_status', $filters['fulfillment']))
            ->when(! empty($filters['payment_method']), fn ($query) => $query->where('payment_method', $filters['payment_method']))
            ->when(! empty($filters['from']), fn ($query) => $query->whereDate('created_at', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn ($query) => $query->whereDate('created_at', '<=', $filters['to']))
            ->tap(function ($query) use ($sort, $dir) {
                if ($sort === 'status') {
                    $query->orderByRaw("CASE status WHEN 'paid' THEN 1 WHEN 'pending' THEN 2 WHEN 'cancelled' THEN 3 ELSE 4 END {$dir}");
                } elseif ($sort === 'fulfillment_status') {
                    $query->orderByRaw("CASE fulfillment_status WHEN 'received' THEN 1 WHEN 'processing' THEN 2 WHEN 'ready' THEN 3 WHEN 'shipped' THEN 4 ELSE 5 END {$dir}");
                } else {
                    $query->orderBy($sort, $dir);
                }
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($this->current->owns($order), 404);
        abort_unless($order->status === 'paid', 404);
        $this->rememberPayment($order);

        return view('admin.orders.show', [
            'order' => $order->fresh(),
        ]);
    }

    public function whatsapp(Request $request, Order $order): RedirectResponse
    {
        abort_unless($this->current->owns($order), 404);
        abort_unless($order->status === 'paid', 404);

        $data = $request->validate([
            'fulfillment_status' => ['required', Rule::in(['received', 'processing', 'ready', 'shipped'])],
            'tracking_number' => ['nullable', 'required_if:fulfillment_status,shipped', 'string', 'max:80'],
            'tracking_company' => ['nullable', 'required_if:fulfillment_status,shipped', 'string', 'max:80'],
        ]);

        $phone = $order->whatsappPhone();
        if ($phone === null) {
            return back()->withErrors(['phone' => 'This order has no phone number for WhatsApp.']);
        }

        $tracking = trim((string) ($data['tracking_number'] ?? ''));
        $company = trim((string) ($data['tracking_company'] ?? ''));
        $order->update([
            'fulfillment_status' => $data['fulfillment_status'],
            'tracking_number' => $data['fulfillment_status'] === 'shipped' ? $tracking : $order->tracking_number,
            'tracking_company' => $data['fulfillment_status'] === 'shipped' ? $company : $order->tracking_company,
        ]);

        return back()->with('whatsapp_url', 'https://wa.me/'.$phone.'?text='.rawurlencode($this->message($order->fresh(), $data['fulfillment_status'], $tracking, $company)));
    }

    private function rememberPayment(Order $order): void
    {
        if ($order->payment_details || ! $order->razorpay_payment_id) {
            return;
        }

        $snapshot = $this->payments->snapshot($order->razorpay_payment_id);
        if (! $snapshot) {
            return;
        }

        $order->update([
            'payment_method' => $snapshot['method'] ?? null,
            'payment_details' => $snapshot,
        ]);
    }

    private function message(Order $order, string $status, string $tracking, string $company = ''): string
    {
        $name = $order->customer_name ?: 'there';
        $store = $order->store?->name ?: 'Richie Rich';
        $ref = $order->razorpay_order_id;
        $amount = '₹'.number_format($order->amount / 100, 0);

        return match ($status) {
            'received' => "Hi {$name}, this is {$store}. We have received your order {$ref} ({$amount}). We will update you as it is prepared.",
            'processing' => "Hi {$name}, your order {$ref} at {$store} is now being processed.",
            'ready' => "Hi {$name}, your order {$ref} at {$store} is ready.",
            'shipped' => "Hi {$name}, your order {$ref} at {$store} has been shipped. {$company} tracking number: {$tracking}.",
            default => "Hi {$name}, an update on your order {$ref} from {$store}.",
        };
    }
}
