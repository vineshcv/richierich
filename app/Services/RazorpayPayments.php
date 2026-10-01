<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RazorpayPayments
{
    /** @return array<string, mixed>|null */
    public function snapshot(string $paymentId): ?array
    {
        $key = (string) config('services.razorpay.key');
        $secret = (string) config('services.razorpay.secret');
        if ($key === '' || $secret === '' || $paymentId === '') {
            return null;
        }

        $response = Http::withBasicAuth($key, $secret)
            ->timeout(12)
            ->get('https://api.razorpay.com/v1/payments/'.$paymentId);

        if (! $response->successful()) {
            return null;
        }

        $payment = $response->json();
        if (! is_array($payment)) {
            return null;
        }

        $card = is_array($payment['card'] ?? null) ? $payment['card'] : [];
        $acquirer = is_array($payment['acquirer_data'] ?? null) ? $payment['acquirer_data'] : [];

        return array_filter([
            'method' => $payment['method'] ?? null,
            'payment_id' => $payment['id'] ?? $paymentId,
            'status' => $payment['status'] ?? null,
            'bank' => $payment['bank'] ?? null,
            'wallet' => $payment['wallet'] ?? null,
            'vpa' => $payment['vpa'] ?? null,
            'card_network' => $card['network'] ?? null,
            'card_type' => $card['type'] ?? null,
            'card_last4' => $card['last4'] ?? null,
            'card_issuer' => $card['issuer'] ?? null,
            'reference' => $acquirer['rrn'] ?? $acquirer['upi_transaction_id'] ?? $acquirer['bank_transaction_id'] ?? null,
            'email' => $payment['email'] ?? null,
            'contact' => $payment['contact'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
