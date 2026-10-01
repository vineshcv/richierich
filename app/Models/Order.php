<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'customer_name',
        'email',
        'phone',
        'shipping_address',
        'amount',
        'currency',
        'status',
        'stock_held',
        'payment_method',
        'payment_details',
        'fulfillment_status',
        'tracking_number',
        'items',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'items' => 'array',
            'shipping_address' => 'array',
            'payment_details' => 'array',
            'stock_held' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'card' => 'Card',
            'upi' => 'UPI',
            'netbanking' => 'Netbanking',
            'wallet' => 'Wallet',
            'emi' => 'EMI',
            'paylater' => 'Pay later',
            null, '' => '—',
            default => ucfirst((string) $this->payment_method),
        };
    }

    public function fulfillmentLabel(): ?string
    {
        return match ($this->fulfillment_status) {
            'received' => 'Order received',
            'processing' => 'Order processing',
            'ready' => 'Order ready',
            'shipped' => 'Order shipped',
            default => null,
        };
    }

    public function whatsappPhone(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone) ?? '';
        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '91'.substr($digits, 1);
        }

        return strlen($digits) >= 11 ? $digits : null;
    }
}
