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

    public function index(): View
    {
        $storeId = $this->current->adminId();

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($this->current->owns($order), 404);
        $this->rememberPayment($order);

        return view('admin.orders.show', [
            'order' => $order->fresh(),
        ]);
    }

    public function whatsapp(Request $request, Order $order): RedirectResponse
    {
        abort_unless($this->current->owns($order), 404);

        $data = $request->validate([
            'fulfillment_status' => ['required', Rule::in(['received', 'processing', 'ready', 'shipped'])],
            'tracking_number' => ['nullable', 'required_if:fulfillment_status,shipped', 'string', 'max:80'],
        ]);

        $phone = $order->whatsappPhone();
        if ($phone === null) {
            return back()->withErrors(['phone' => 'This order has no phone number for WhatsApp.']);
        }

        $tracking = trim((string) ($data['tracking_number'] ?? ''));
        $order->update([
            'fulfillment_status' => $data['fulfillment_status'],
            'tracking_number' => $data['fulfillment_status'] === 'shipped' ? $tracking : $order->tracking_number,
        ]);

        return back()->with('whatsapp_url', 'https://wa.me/'.$phone.'?text='.rawurlencode($this->message($order->fresh(), $data['fulfillment_status'], $tracking)));
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

    private function message(Order $order, string $status, string $tracking): string
    {
        $name = $order->customer_name ?: 'there';
        $store = $order->store?->name ?: 'Richie Rich';
        $ref = $order->razorpay_order_id;
        $amount = '₹'.number_format($order->amount / 100, 0);

        return match ($status) {
            'received' => "Hi {$name}, this is {$store}. We have received your order {$ref} ({$amount}). We will update you as it is prepared.",
            'processing' => "Hi {$name}, your order {$ref} at {$store} is now being processed.",
            'ready' => "Hi {$name}, your order {$ref} at {$store} is ready.",
            'shipped' => "Hi {$name}, your order {$ref} at {$store} has been shipped. Tracking number: {$tracking}.",
            default => "Hi {$name}, an update on your order {$ref} from {$store}.",
        };
    }
}
