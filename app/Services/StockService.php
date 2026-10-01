<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function sweepExpired(): void
    {
        Order::query()
            ->where('status', 'pending')
            ->where('stock_held', true)
            ->where('created_at', '<', now()->subHour())
            ->orderBy('id')
            ->get()
            ->each(fn (Order $order) => $this->release($order));
    }

    /** @param  array<string, int>  $qtyBySlug */
    public function takeLocked(array $qtyBySlug): ?string
    {
        $products = Product::query()
            ->whereIn('slug', array_keys($qtyBySlug))
            ->lockForUpdate()
            ->get()
            ->keyBy('slug');

        foreach ($qtyBySlug as $slug => $qty) {
            $product = $products->get($slug);
            if (! $product || $product->stock === null) {
                continue;
            }
            if ((int) $product->stock < $qty) {
                $left = (int) $product->stock;

                return $left < 1
                    ? $product->name.' is out of stock.'
                    : 'Only '.$left.' left for '.$product->name.'.';
            }
        }

        foreach ($qtyBySlug as $slug => $qty) {
            $product = $products->get($slug);
            if ($product && $product->stock !== null) {
                $product->decrement('stock', $qty);
            }
        }

        return null;
    }

    public function release(Order $order): void
    {
        if (! $order->stock_held || $order->status !== 'pending') {
            return;
        }

        DB::transaction(function () use ($order) {
            $fresh = Order::query()->whereKey($order->id)->lockForUpdate()->first();
            if (! $fresh || ! $fresh->stock_held || $fresh->status !== 'pending') {
                return;
            }

            foreach ($fresh->items ?? [] as $line) {
                $slug = $line['slug'] ?? null;
                $qty = (int) ($line['qty'] ?? 0);
                if (! $slug || $qty < 1) {
                    continue;
                }
                Product::query()->where('slug', $slug)->whereNotNull('stock')->increment('stock', $qty);
            }

            $fresh->update([
                'stock_held' => false,
                'status' => 'cancelled',
            ]);
        });
    }

    public function releasePending(string $razorpayOrderId): void
    {
        Order::query()
            ->where('razorpay_order_id', $razorpayOrderId)
            ->where('status', 'pending')
            ->where('stock_held', true)
            ->get()
            ->each(fn (Order $order) => $this->release($order));
    }
}
