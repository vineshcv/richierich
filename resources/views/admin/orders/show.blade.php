@extends('layouts.admin')

@section('title', 'Order')
@section('heading', $order->customer_name ?: 'Order')
@section('subheading', $order->razorpay_order_id)
@section('actions')
  <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
@php
  $pay = $order->payment_details ?? [];
  $cardBits = collect([
      $pay['card_network'] ?? null,
      $pay['card_type'] ?? null,
      ! empty($pay['card_last4']) ? '•••• '.$pay['card_last4'] : null,
      $pay['card_issuer'] ?? null,
  ])->filter();
@endphp

<div class="card">
  @if(session('whatsapp_url'))
    <p class="order-wa-open">Status saved. <a class="btn btn-primary" href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener">Open WhatsApp</a></p>
  @endif
  <div class="form-grid">
    <div class="field">
      <label>Payment status</label>
      <div><span class="badge {{ $order->status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($order->status) }}</span></div>
    </div>
    <div class="field">
      <label>Order update</label>
      <div>{{ $order->fulfillmentLabel() ?: 'Not sent yet' }}</div>
    </div>
    <div class="field">
      <label>Total</label>
      <div><strong>₹{{ number_format($order->amount / 100, 0) }}</strong></div>
    </div>
    <div class="field">
      <label>Email</label>
      <div>{{ $order->email ?: '—' }}</div>
    </div>
    <div class="field">
      <label>Phone</label>
      <div>{{ $order->phone ?: '—' }}</div>
    </div>
    @if($order->tracking_company)
      <div class="field">
        <label>Tracking company</label>
        <div>{{ $order->tracking_company }}</div>
      </div>
    @endif
    @if($order->tracking_number)
      <div class="field">
        <label>Tracking number</label>
        <div>{{ $order->tracking_number }}</div>
      </div>
    @endif
  </div>

  <h2 class="card-title" style="margin-top:1.25rem;">Payment</h2>
  <div class="form-grid">
    <div class="field">
      <label>Method</label>
      <div>{{ $order->paymentMethodLabel() }}</div>
    </div>
    <div class="field">
      <label>Razorpay payment ID</label>
      <div>{{ $order->razorpay_payment_id ?: '—' }}</div>
    </div>
    <div class="field">
      <label>Razorpay order ID</label>
      <div>{{ $order->razorpay_order_id }}</div>
    </div>
    @if($cardBits->isNotEmpty())
      <div class="field">
        <label>Card</label>
        <div>{{ $cardBits->implode(' · ') }}</div>
      </div>
    @endif
    @if(!empty($pay['vpa']))
      <div class="field">
        <label>UPI</label>
        <div>{{ $pay['vpa'] }}</div>
      </div>
    @endif
    @if(!empty($pay['bank']))
      <div class="field">
        <label>Bank</label>
        <div>{{ $pay['bank'] }}</div>
      </div>
    @endif
    @if(!empty($pay['wallet']))
      <div class="field">
        <label>Wallet</label>
        <div>{{ ucfirst($pay['wallet']) }}</div>
      </div>
    @endif
    @if(!empty($pay['reference']))
      <div class="field">
        <label>Bank reference</label>
        <div>{{ $pay['reference'] }}</div>
      </div>
    @endif
  </div>
  @if($order->status === 'paid' && ! $order->payment_details)
    <p class="muted" style="margin:0.75rem 0 0;">Payment method details are not stored for this order yet.</p>
  @endif

  @php $ship = $order->shipping_address ?? []; @endphp
  <h2 class="card-title" style="margin-top:1.25rem;">Shipping address</h2>
  <p style="margin:0;">
    {{ trim(($ship['first_name'] ?? '').' '.($ship['last_name'] ?? '')) ?: ($order->customer_name ?: '—') }}<br />
    {{ $ship['address_1'] ?? '' }}
    @if(!empty($ship['address_2']))<br />{{ $ship['address_2'] }}@endif
    <br />
    {{ collect([$ship['city'] ?? null, $ship['state'] ?? null, $ship['postcode'] ?? null, $ship['country'] ?? null])->filter()->implode(', ') ?: '—' }}
  </p>
</div>

<div class="card" style="margin-top:1rem;">
  <h2 class="card-title">Send status on WhatsApp</h2>
  <p class="muted" style="margin-top:0;">Opens WhatsApp with a message to the customer. The status is saved on this order.</p>
  @error('phone')<div class="error">{{ $message }}</div>@enderror
  @error('tracking_number')<div class="error">{{ $message }}</div>@enderror
  @error('tracking_company')<div class="error">{{ $message }}</div>@enderror

  @if($order->whatsappPhone())
    <div class="order-wa-actions">
      @foreach(['received' => 'Order received', 'processing' => 'Order processing', 'ready' => 'Order ready'] as $key => $label)
        <form method="POST" action="{{ route('admin.orders.whatsapp', $order) }}">
          @csrf
          <input type="hidden" name="fulfillment_status" value="{{ $key }}" />
          <button type="submit" class="btn btn-secondary">{{ $label }}</button>
        </form>
      @endforeach
    </div>

    <form method="POST" action="{{ route('admin.orders.whatsapp', $order) }}" class="order-wa-ship">
      @csrf
      <input type="hidden" name="fulfillment_status" value="shipped" />
      <div class="field">
        <label for="tracking_company">Tracking company</label>
        <input id="tracking_company" type="text" name="tracking_company" value="{{ old('tracking_company', $order->tracking_company) }}" placeholder="Delhivery, India Post, DTDC…" required />
      </div>
      <div class="field">
        <label for="tracking_number">Tracking number</label>
        <input id="tracking_number" type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Enter tracking number" required />
      </div>
      <button type="submit" class="btn btn-primary">Order shipped</button>
    </form>
  @else
    <p class="muted">Add a customer phone number before sending a WhatsApp update.</p>
  @endif
</div>

<div class="card" style="margin-top:1rem;">
  <h2 class="card-title">Items</h2>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
      </thead>
      <tbody>
        @forelse($order->items ?? [] as $item)
          <tr>
            <td>{{ $item['name'] ?? $item['slug'] ?? 'Item' }}</td>
            <td>{{ $item['qty'] ?? 1 }}</td>
            <td>₹{{ number_format((($item['unit_paise'] ?? 0) * ($item['qty'] ?? 1)) / 100, 0) }}</td>
          </tr>
        @empty
          <tr><td colspan="3" class="muted">No items stored.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
@if(session('whatsapp_url'))
<script>window.open(@json(session('whatsapp_url')), '_blank', 'noopener');</script>
@endif
@endpush
