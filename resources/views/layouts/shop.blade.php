<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', ($settings->store_name ?? 'Richie Rich').' Boutique — Ethnic & western dresses · WhatsApp enquire')</title>
  <meta name="description" content="@yield('meta_description', 'Shop sarees, kurtis, lehengas and western dresses. Enquire on WhatsApp.')" />
  <meta property="og:image" content="{{ asset('assets/b1.png') }}" />
  <link rel="icon" href="{{ asset('assets/dress_logo.png') }}" type="image/png" />
  <link rel="manifest" href="{{ asset('manifest.webmanifest') }}" />
  <meta name="theme-color" content="#9f1239" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="{{ $settings->store_name ?? 'Richie Rich' }}" />
  <link rel="apple-touch-icon" href="{{ asset('assets/dress_apple-touch-icon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/dress_styles.css') }}?v=68" />
  @include('partials.dress-config')
  <script src="{{ asset('assets/dress_loader.js') }}?v=5"></script>
  @stack('head')
</head>
<body class="@yield('body_class')" data-dress-page="@yield('dress_page', 'home')">
  <header class="site-header">
    <div class="container navbar">
      <a class="logo" href="{{ route('home') }}"><img src="{{ asset('assets/dress_logo.png') }}" alt="{{ $settings->store_name ?? 'Richie Rich' }} Boutique" /></a>
      <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <nav class="nav-menu" aria-label="Primary">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('shop.products') }}" class="{{ request()->routeIs('shop.products') || request()->routeIs('shop.show') ? 'active' : '' }}">Shop</a>
        <a href="{{ route('shop.contact') }}" class="{{ request()->routeIs('shop.contact') ? 'active' : '' }}">Contact</a>
        @if($settings->show_cart ?? true)
          <a class="btn btn-primary nav-view-cart" href="{{ route('shop.cart') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            View cart
            <span data-cart-count hidden>0</span>
          </a>
        @endif
      </nav>
    </div>
  </header>

  <main>@yield('content')</main>

  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="footer-brand">
        <a class="logo" href="{{ route('home') }}"><img src="{{ asset('assets/dress_logo.png') }}" alt="{{ $settings->store_name ?? 'Richie Rich' }} Boutique" /></a>
        <p>Ethnic & western dresses, look sets and festive edits — enquire on WhatsApp.</p>
      </div>
      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('shop.products') }}">Shop</a></li>
          <li><a href="{{ route('shop.combos') }}">Sets</a></li>
          <li><a href="{{ route('shop.season') }}">Festive edit</a></li>
          <li><a href="{{ route('shop.contact') }}">Contact</a></li>
          <li><a href="{{ route('shop.cart') }}">Cart</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Categories</h4>
        <ul>
          @foreach(($footerCategories ?? []) as $cat)
            <li><a href="{{ route('shop.products', ['cat' => $cat->slug]) }}">{{ $cat->name }}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <ul>
          @php $wa = preg_replace('/\D+/', '', $settings->whatsapp_number ?? ''); @endphp
          @if(($settings->show_whatsapp ?? true) && $wa)
            <li><a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">WhatsApp</a></li>
            <li><a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">+{{ $wa }}</a></li>
          @endif
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">Copyright © {{ date('Y') }} {{ $settings->store_name ?? 'Richie Rich' }} Boutique. All Rights Reserved. Designed and developed by Azores Interactive.</div>
    </div>
  </footer>

  <script src="{{ asset('assets/dress_main.js') }}?v=68"></script>
  <script src="{{ asset('assets/dress_cart.js') }}?v=5"></script>
  @stack('scripts')
  <script src="{{ asset('assets/dress_pwa.js') }}?v=2"></script>
  <script src="{{ asset('assets/azores_share.js') }}?v=4"></script>
</body>
</html>
