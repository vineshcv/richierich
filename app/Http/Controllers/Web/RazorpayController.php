<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\RazorpayPayments;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RazorpayController extends Controller
{
    public function __construct(private StockService $stocks)
    {
    }

    public function create(Request $request): JsonResponse
    {
        $key = (string) config('services.razorpay.key');
        $secret = (string) config('services.razorpay.secret');
        if ($key === '' || $secret === '') {
            return response()->json(['message' => 'Razorpay is not configured.'], 503);
        }

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.id' => ['required', 'string', 'max:120'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:500'],
            'shipping.first_name' => ['required', 'string', 'max:80'],
            'shipping.last_name' => ['required', 'string', 'max:80'],
            'shipping.address_1' => ['required', 'string', 'max:190'],
            'shipping.address_2' => ['nullable', 'string', 'max:190'],
            'shipping.city' => ['required', 'string', 'max:80'],
            'shipping.state' => ['required', 'string', 'max:80'],
            'shipping.postcode' => ['required', 'regex:/^[1-9][0-9]{5}$/'],
            'shipping.country' => ['required', 'in:India'],
            'shipping.phone' => ['required', 'regex:/^(?:\+91[\s-]?)?[6-9][0-9]{9}$/'],
            'shipping.email' => ['required', 'email', 'max:190'],
            'create_account' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
        ], [
            'shipping.postcode.regex' => 'Enter a 6-digit PIN code.',
            'shipping.phone.regex' => 'Enter a valid 10-digit mobile number.',
            'password.min' => 'Account password must be at least 8 characters.',
        ]);

        $qtyBySlug = [];
        foreach ($data['items'] as $line) {
            $qtyBySlug[$line['id']] = ($qtyBySlug[$line['id']] ?? 0) + $line['qty'];
        }

        $products = Product::query()
            ->whereIn('slug', array_keys($qtyBySlug))
            ->where('status', 'active')
            ->get()
            ->keyBy('slug');

        $groups = [];
        $paise = 0;
        foreach ($qtyBySlug as $slug => $qty) {
            $product = $products->get($slug);
            if (! $product || ! $product->show_price || $product->price === null) {
                return response()->json(['message' => 'One of the items cannot be paid online.'], 422);
            }
            $unit = (int) round(((float) $product->price) * 100);
            if ($unit < 100) {
                return response()->json(['message' => $product->name.' does not have a payable price.'], 422);
            }
            $linePaise = $unit * $qty;
            $paise += $linePaise;
            $storeId = (int) $product->store_id;
            $groups[$storeId]['paise'] = ($groups[$storeId]['paise'] ?? 0) + $linePaise;
            $groups[$storeId]['lines'][] = [
                'slug' => $product->slug,
                'name' => $product->name,
                'qty' => $qty,
                'unit_paise' => $unit,
            ];
        }

        if ($paise < 100) {
            return response()->json(['message' => 'Order total is too small to pay.'], 422);
        }

        $shipping = $data['shipping'];
        $shipping['address_2'] = trim((string) ($shipping['address_2'] ?? ''));
        $shipping['country'] = 'India';
        $fullName = trim($shipping['first_name'].' '.$shipping['last_name']);
        $account = $this->attachCustomer($request, $shipping, (bool) ($data['create_account'] ?? false), $data['password'] ?? null);
        if ($account instanceof JsonResponse) {
            return $account;
        }

        $this->stocks->sweepExpired();
        $holdId = 'hold_'.Str::lower(Str::random(16));

        try {
            DB::transaction(function () use ($qtyBySlug, $groups, $holdId, $account, $fullName, $shipping) {
                $shortage = $this->stocks->takeLocked($qtyBySlug);
                if ($shortage) {
                    throw new \RuntimeException($shortage);
                }

                foreach ($groups as $storeId => $group) {
                    Order::query()->create([
                        'store_id' => $storeId ?: null,
                        'user_id' => $account,
                        'razorpay_order_id' => $holdId,
                        'customer_name' => $fullName,
                        'email' => $shipping['email'],
                        'phone' => $shipping['phone'],
                        'shipping_address' => $shipping,
                        'amount' => $group['paise'],
                        'currency' => 'INR',
                        'status' => 'pending',
                        'stock_held' => true,
                        'items' => $group['lines'],
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $response = Http::withBasicAuth($key, $secret)
            ->acceptJson()
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $paise,
                'currency' => 'INR',
                'receipt' => 'rr'.Str::lower(Str::random(12)),
            ]);

        $razorpayOrderId = $response->successful() ? (string) $response->json('id') : '';
        if ($razorpayOrderId === '') {
            $this->stocks->releasePending($holdId);

            return response()->json(['message' => 'Could not start the payment. Try again.'], 502);
        }

        Order::query()->where('razorpay_order_id', $holdId)->update([
            'razorpay_order_id' => $razorpayOrderId,
        ]);

        return response()->json([
            'key' => $key,
            'order_id' => $razorpayOrderId,
            'amount' => $paise,
            'currency' => 'INR',
            'name' => 'Richie Rich Boutique',
            'csrf' => csrf_token(),
            'prefill' => [
                'name' => $fullName,
                'email' => $shipping['email'],
                'contact' => preg_replace('/\D+/', '', $shipping['phone']),
            ],
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return response()->json(['message' => 'Those details do not match a customer account.'], 422);
        }

        $user = $request->user();
        if (! $user || $user->role !== 'customer') {
            Auth::logout();

            return response()->json(['message' => 'Those details do not match a customer account.'], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Logged in.',
            'name' => $user->name,
            'email' => $user->email,
            'csrf' => csrf_token(),
        ]);
    }

    private function attachCustomer(Request $request, array $shipping, bool $createAccount, ?string $password): int|JsonResponse|null
    {
        $current = $request->user();
        if ($current) {
            $this->syncCustomerEmail($current, $shipping['email']);

            return $current->id;
        }

        if (! $createAccount) {
            return null;
        }

        if ($password === null || $password === '') {
            return response()->json(['message' => 'Enter a password to create an account.'], 422);
        }

        $email = Str::lower($shipping['email']);
        if (User::query()->where('email', $email)->exists()) {
            return response()->json([
                'message' => 'An account is already registered with your email address. Please log in.',
            ], 422);
        }

        $user = User::query()->create([
            'name' => trim($shipping['first_name'].' '.$shipping['last_name']),
            'email' => $email,
            'password' => $password,
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $user->id;
    }

    private function syncCustomerEmail(User $user, string $email): void
    {
        if ($user->role !== 'customer') {
            return;
        }

        $email = Str::lower(trim($email));
        if ($email === '' || $user->email === $email) {
            return;
        }

        if (User::query()->where('email', $email)->whereKeyNot($user->id)->exists()) {
            return;
        }

        $user->update(['email' => $email]);
    }

    public function verify(Request $request): JsonResponse
    {
        $secret = (string) config('services.razorpay.secret');
        if ($secret === '') {
            return response()->json(['message' => 'Razorpay is not configured.'], 503);
        }

        $data = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $expected = hash_hmac('sha256', $data['razorpay_order_id'].'|'.$data['razorpay_payment_id'], $secret);
        if (! hash_equals($expected, $data['razorpay_signature'])) {
            return response()->json(['message' => 'Payment could not be verified.'], 422);
        }

        $orders = Order::query()->where('razorpay_order_id', $data['razorpay_order_id'])->where('status', 'pending')->get();
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Order was not found.'], 404);
        }

        $snapshot = app(RazorpayPayments::class)->snapshot($data['razorpay_payment_id']);

        foreach ($orders as $order) {
            $order->update([
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'status' => 'paid',
                'stock_held' => false,
                'payment_method' => $snapshot['method'] ?? $order->payment_method,
                'payment_details' => $snapshot ?: $order->payment_details,
            ]);
        }

        $this->notifyStore($orders);

        return response()->json([
            'message' => 'Payment received.',
            'order_id' => $data['razorpay_order_id'],
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Order>  $orders
     */
    private function notifyStore($orders): void
    {
        $recipients = collect(preg_split('/\s*,\s*/', (string) config('services.orders.notify_email')) ?: [])
            ->map(fn (string $email) => strtolower(trim($email)))
            ->filter(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if ($recipients === [] || $orders->isEmpty()) {
            return;
        }

        try {
            Mail::to($recipients)->send(new OrderPlacedMail($orders));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
