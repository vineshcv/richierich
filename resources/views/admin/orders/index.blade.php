@extends('layouts.admin')

@section('title', 'Orders')
@section('heading', 'Orders')
@section('subheading', 'Orders for the store selected above')

@section('content')
<div class="card">
  <div class="table-wrap desktop-only">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Update</th>
          <th>Date</th>
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
            <td>
              <span class="badge {{ $order->status === 'paid' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($order->status) }}</span>
              @if($order->fulfillmentLabel())
                <span class="badge badge-muted">{{ $order->fulfillmentLabel() }}</span>
              @endif
            </td>
            <td>{{ $order->created_at?->format('d M Y, h:i A') }}</td>
            <td class="actions">
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.orders.show', $order) }}">View</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="muted">No orders for this store yet.</td></tr>
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
            <span>{{ $order->fulfillmentLabel() ?: ucfirst($order->status) }}</span>
            <span>{{ $order->created_at?->format('d M Y') }}</span>
          </div>
        </div>
        <div class="mobile-item-actions">
          <a class="btn btn-secondary btn-sm" href="{{ route('admin.orders.show', $order) }}">View</a>
        </div>
      </article>
    @empty
      <p class="muted">No orders for this store yet.</p>
    @endforelse
  </div>

  <div class="pagination">{{ $orders->links() }}</div>
</div>
@endsection
