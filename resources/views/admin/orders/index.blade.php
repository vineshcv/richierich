@extends('layouts.admin')

@section('title', 'Orders')
@section('heading', 'Orders')
@section('subheading', 'Orders for the store selected above')

@section('content')
@php
  $sortUrl = function (string $column) use ($sort, $dir) {
      $next = ($sort === $column && $dir === 'asc') ? 'desc' : 'asc';
      if ($sort !== $column && in_array($column, ['created_at', 'amount'], true)) {
          $next = 'desc';
      }

      return request()->fullUrlWithQuery(['sort' => $column, 'dir' => $next, 'page' => null]);
  };
  $filtering = request()->hasAny(['q', 'fulfillment', 'payment_method', 'from', 'to']);
@endphp
<div class="card">
  <form method="GET" action="{{ route('admin.orders.index') }}" class="order-filters">
    <div class="field">
      <label for="q">Search</label>
      <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Name, phone, email, order id" />
    </div>
    <div class="field">
      <label for="fulfillment">Order status</label>
      <select id="fulfillment" name="fulfillment">
        <option value="">All</option>
        <option value="none" @selected(request('fulfillment') === 'none')>Not updated</option>
        @foreach(['received' => 'Order received', 'processing' => 'Order processing', 'ready' => 'Order ready', 'shipped' => 'Order shipped'] as $value => $label)
          <option value="{{ $value }}" @selected(request('fulfillment') === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="payment_method">Payment method</label>
      <select id="payment_method" name="payment_method">
        <option value="">All</option>
        @foreach(['card' => 'Card', 'upi' => 'UPI', 'netbanking' => 'Netbanking', 'wallet' => 'Wallet', 'emi' => 'EMI', 'paylater' => 'Pay later'] as $value => $label)
          <option value="{{ $value }}" @selected(request('payment_method') === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="from">From</label>
      <input id="from" type="date" name="from" value="{{ request('from') }}" />
    </div>
    <div class="field">
      <label for="to">To</label>
      <input id="to" type="date" name="to" value="{{ request('to') }}" />
    </div>
    <div class="field">
      <label for="sort">Sort</label>
      <select id="sort" name="sort">
        @foreach([
          'created_at' => 'Date',
          'customer_name' => 'Customer',
          'amount' => 'Total',
          'status' => 'Payment status',
          'fulfillment_status' => 'Order status',
          'payment_method' => 'Payment method',
        ] as $value => $label)
          <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="dir">Direction</label>
      <select id="dir" name="dir">
        <option value="desc" @selected($dir === 'desc')>Newest / high first</option>
        <option value="asc" @selected($dir === 'asc')>Oldest / low first</option>
      </select>
    </div>
    <div class="field">
      <label for="per_page">Per page</label>
      <select id="per_page" name="per_page">
        @foreach([10, 20, 50] as $size)
          <option value="{{ $size }}" @selected((int) request('per_page', 20) === $size)>{{ $size }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label>&nbsp;</label>
      <div class="order-filters-actions">
        <button type="submit" class="btn btn-secondary">Apply</button>
        @if($filtering || request('sort') || request('per_page'))
          <a class="btn btn-secondary" href="{{ route('admin.orders.index') }}">Clear</a>
        @endif
      </div>
    </div>
  </form>

  <div class="table-wrap desktop-only">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Order</th>
          <th><a class="sort-link {{ $sort === 'customer_name' ? 'is-active' : '' }}" href="{{ $sortUrl('customer_name') }}">Customer</a></th>
          <th>Phone</th>
          <th><a class="sort-link {{ $sort === 'amount' ? 'is-active' : '' }}" href="{{ $sortUrl('amount') }}">Total</a></th>
          <th><a class="sort-link {{ $sort === 'payment_method' ? 'is-active' : '' }}" href="{{ $sortUrl('payment_method') }}">Payment</a></th>
          <th><a class="sort-link {{ $sort === 'status' ? 'is-active' : '' }}" href="{{ $sortUrl('status') }}">Status</a></th>
          <th><a class="sort-link {{ $sort === 'fulfillment_status' ? 'is-active' : '' }}" href="{{ $sortUrl('fulfillment_status') }}">Update</a></th>
          <th><a class="sort-link {{ $sort === 'created_at' ? 'is-active' : '' }}" href="{{ $sortUrl('created_at') }}">Date</a></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td class="muted">{{ $order->razorpay_order_id }}</td>
            <td><strong>{{ $order->customer_name ?: '—' }}</strong></td>
            <td>{{ $order->phone ?: '—' }}</td>
            <td>₹{{ number_format($order->amount / 100, 0) }}</td>
            <td>{{ $order->paymentMethodLabel() }}</td>
            <td><span class="badge {{ $order->status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($order->status) }}</span></td>
            <td>
              @if($order->fulfillmentLabel())
                <span class="badge badge-muted">{{ $order->fulfillmentLabel() }}</span>
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td>{{ $order->created_at?->format('d M Y, h:i A') }}</td>
            <td class="actions">
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.orders.show', $order) }}">View</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="muted">{{ $filtering ? 'No orders match these filters.' : 'No orders for this store yet.' }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mobile-list">
    @forelse($orders as $order)
      <article class="mobile-item">
        <div class="mobile-item-body">
          <strong>{{ $order->customer_name ?: 'Order' }}</strong>
          <span class="badge {{ $order->status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($order->status) }}</span>
          <div class="mobile-item-meta">
            <span>₹{{ number_format($order->amount / 100, 0) }}</span>
            <span>{{ $order->paymentMethodLabel() }}</span>
            <span>{{ $order->fulfillmentLabel() ?: 'No update' }}</span>
            <span>{{ $order->created_at?->format('d M Y') }}</span>
          </div>
        </div>
        <div class="mobile-item-actions">
          <a class="btn btn-secondary btn-sm" href="{{ route('admin.orders.show', $order) }}">View</a>
        </div>
      </article>
    @empty
      <p class="muted">{{ $filtering ? 'No orders match these filters.' : 'No orders for this store yet.' }}</p>
    @endforelse
  </div>

  <div class="pagination">{{ $orders->links('admin.partials.pagination') }}</div>
</div>
@endsection
