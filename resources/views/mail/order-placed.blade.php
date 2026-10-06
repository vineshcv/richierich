@php
  $first = $orders->first();
  $ship = $first->shipping_address ?? [];
@endphp
<p>A new paid order has come in on the website.</p>

<p>
  <strong>Customer:</strong> {{ $first->customer_name ?: '—' }}<br>
  <strong>Email:</strong> {{ $first->email ?: '—' }}<br>
  <strong>Phone:</strong> {{ $first->phone ?: '—' }}
</p>

<p>
  <strong>Shipping</strong><br>
  {{ trim(($ship['first_name'] ?? '').' '.($ship['last_name'] ?? '')) ?: ($first->customer_name ?: '—') }}<br>
  {{ $ship['address_1'] ?? '' }}
  @if(!empty($ship['address_2']))<br>{{ $ship['address_2'] }}@endif
  <br>
  {{ collect([$ship['city'] ?? null, $ship['state'] ?? null, $ship['postcode'] ?? null, $ship['country'] ?? null])->filter()->implode(', ') ?: '—' }}
</p>

@foreach($orders as $order)
  <p>
    <strong>Order {{ $order->razorpay_order_id }}</strong><br>
    Total: ₹{{ number_format($order->amount / 100, 0) }}
  </p>
  <ul>
    @foreach($order->items ?? [] as $item)
      <li>{{ $item['name'] ?? $item['slug'] ?? 'Item' }} × {{ $item['qty'] ?? 1 }} — ₹{{ number_format((($item['unit_paise'] ?? 0) * ($item['qty'] ?? 1)) / 100, 0) }}</li>
    @endforeach
  </ul>
@endforeach
