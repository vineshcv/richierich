@extends('layouts.shop')

@section('dress_page', 'cart')
@section('body_class', 'page-cart')
@section('title', 'Cart — '.($settings->store_name ?? 'Richie Rich').' Boutique')

@section('content')
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Cart</span>
    </div>
    <h1>Your cart</h1>
  </div>
</section>

<section class="section">
  <div class="container cart-layout">
    <div id="cart-items"></div>
    <aside class="cart-summary">
      <h2>Order summary</h2>
      <div class="cart-summary-row"><span>Items</span><span id="summary-count">0</span></div>
      <div class="cart-summary-row total"><span>Total</span><span id="summary-total">₹0</span></div>
      <div class="cart-summary-actions">
        <button type="button" class="cart-action-ico cart-action-wa" id="btn-wa-cart" aria-label="Enquire on WhatsApp" title="Enquire on WhatsApp">
          <svg class="ico-wa" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.5 0 .2 5.3.2 11.82c0 2.08.55 4.11 1.6 5.9L0 24l6.45-1.69a11.8 11.8 0 005.58 1.42h.01c6.54 0 11.84-5.3 11.84-11.82 0-3.16-1.23-6.13-3.36-8.43zM12.05 21.5h-.01a9.7 9.7 0 01-4.94-1.35l-.35-.21-3.82 1 1.02-3.72-.23-.38a9.7 9.7 0 01-1.5-5.18c0-5.36 4.36-9.72 9.73-9.72a9.66 9.66 0 016.88 2.85 9.66 9.66 0 012.85 6.88c0 5.36-4.37 9.73-9.73 9.73zm5.33-7.28c-.29-.15-1.72-.85-1.99-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.14-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.43-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.43-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.57-.48-.49-.66-.5h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.11-.26-.18-.55-.33z"/></svg>
        </button>
        <button type="button" class="cart-action-ico cart-action-clear" id="btn-clear-cart" aria-label="Clear cart" title="Clear cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6m4 4v7m6-7v7"/></svg>
        </button>
        <a href="{{ route('shop.products') }}" class="btn btn-ghost cart-action-continue">Continue shopping</a>
      </div>
    </aside>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/dress_cart-page.js') }}?v=7"></script>
@endpush
