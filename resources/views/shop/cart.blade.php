@extends('layouts.shop')

@section('dress_page', 'cart')
@section('body_class', 'page-cart')
@section('title', 'Cart — Richierich')

@push('head')
  @include('partials.playfair')
@endpush

@section('content')
<section class="shop-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Cart</span>
    </div>
    <h1>Your Cart</h1>
  </div>
</section>

<section class="section">
  <div class="container cart-layout">
    <div class="cart-main">
      <div id="cart-items"></div>
      <form id="checkout-form" class="checkout-box" hidden data-login="{{ route('checkout.login') }}" novalidate>
        @guest
          <p class="checkout-returning">Returning customer? <button type="button" id="checkout-login-toggle">Click here to login</button></p>
          <div id="checkout-login" class="checkout-login" hidden>
            <div class="field">
              <label for="login-email">Email address</label>
              <input id="login-email" type="email" autocomplete="username" placeholder="you@email.com" />
            </div>
            <div class="field">
              <label for="login-password">Password</label>
              <input id="login-password" type="password" autocomplete="current-password" />
            </div>
            <button type="button" class="btn btn-ghost" id="checkout-login-submit">Login</button>
            <p class="checkout-login-status" id="checkout-login-status" hidden></p>
          </div>
        @endguest
        @auth
          <p class="checkout-as">Checking out as <strong>{{ auth()->user()->email }}</strong></p>
        @endauth

        <h2>Shipping address</h2>
        @php
          $shopName = trim((string) (auth()->user()->name ?? ''));
          $nameParts = $shopName === '' ? [] : preg_split('/\s+/', $shopName, 2);
          $states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'];
        @endphp
        <div class="form-grid checkout-grid">
          <div class="field">
            <label for="ship-first">First name <span class="req">*</span></label>
            <input id="ship-first" name="first_name" required autocomplete="given-name" value="{{ $nameParts[0] ?? '' }}" />
          </div>
          <div class="field">
            <label for="ship-last">Last name <span class="req">*</span></label>
            <input id="ship-last" name="last_name" required autocomplete="family-name" value="{{ $nameParts[1] ?? '' }}" />
          </div>
          <div class="field full">
            <label for="ship-country">Country / Region <span class="req">*</span></label>
            <select id="ship-country" name="country" required>
              <option value="India" selected>India</option>
            </select>
          </div>
          <div class="field full">
            <label for="ship-address">Street address <span class="req">*</span></label>
            <input id="ship-address" name="address_1" required autocomplete="address-line1" placeholder="House number and street name" />
          </div>
          <div class="field full">
            <label class="sr-only" for="ship-address-2">Apartment</label>
            <input id="ship-address-2" name="address_2" autocomplete="address-line2" placeholder="Apartment, suite, unit, etc. (optional)" />
          </div>
          <div class="field">
            <label for="ship-city">Town / City <span class="req">*</span></label>
            <input id="ship-city" name="city" required autocomplete="address-level2" />
          </div>
          <div class="field">
            <label for="ship-state">State <span class="req">*</span></label>
            <select id="ship-state" name="state" required autocomplete="address-level1">
              <option value="">Select a state…</option>
              @foreach($states as $state)
                <option value="{{ $state }}">{{ $state }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label for="ship-pin">PIN code <span class="req">*</span></label>
            <input id="ship-pin" name="postcode" required inputmode="numeric" autocomplete="postal-code" maxlength="6" placeholder="6-digit PIN" />
          </div>
          <div class="field">
            <label for="ship-phone">Phone <span class="req">*</span></label>
            <input id="ship-phone" name="phone" required inputmode="tel" autocomplete="tel" placeholder="10-digit mobile" />
          </div>
          <div class="field full">
            <label for="ship-email">Email address <span class="req">*</span></label>
            <input id="ship-email" name="email" type="email" required autocomplete="email" value="{{ auth()->user()->email ?? '' }}" />
          </div>
        </div>

        @guest
          <label class="checkout-create">
            <input type="checkbox" id="create-account" name="create_account" value="1" />
            Create an account?
          </label>
          <div id="account-fields" class="checkout-account" hidden>
            <p>Create an account by entering a password below. If you are a returning customer please login at the top of the page.</p>
            <div class="field">
              <label for="account-password">Account password <span class="req">*</span></label>
              <input id="account-password" name="password" type="password" autocomplete="new-password" minlength="8" />
            </div>
          </div>
        @endguest
      </form>
    </div>
    <aside class="cart-summary">
      <h2>Order summary</h2>
      <div class="cart-summary-row"><span>Items</span><span id="summary-count">0</span></div>
      <div class="cart-summary-row total"><span>Total</span><span id="summary-total">₹0</span></div>
      <button type="button" class="btn btn-primary cart-pay" id="btn-pay" hidden data-create="{{ route('checkout.razorpay') }}" data-verify="{{ route('checkout.razorpay.verify') }}">Pay now</button>
      <p class="cart-pay-note" id="cart-pay-status" hidden></p>
      <div class="cart-summary-actions">
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
<script src="{{ asset('assets/dress_cart-page.js') }}?v=11"></script>
@endpush
