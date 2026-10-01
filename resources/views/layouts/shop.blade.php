<!DOCTYPE html>
<html lang="en" @if(request()->routeIs('home')) data-loader="1" @endif>
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Richierich — Ethnic & western dresses · WhatsApp enquire')</title>
  <meta name="description" content="@yield('meta_description', 'Shop sarees, kurtis, lehengas and western dresses. Enquire on WhatsApp.')" />
  <meta property="og:image" content="{{ asset('assets/b1.png') }}" />
  <link rel="icon" href="{{ asset('favicon.ico') }}?v=3" sizes="any" />
  <link rel="icon" href="{{ asset('assets/dress_icon-192.png') }}?v=3" type="image/png" />
  <link rel="manifest" href="{{ asset('manifest.webmanifest') }}" />
  <meta name="theme-color" content="#26140a" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="default" />
  <meta name="apple-mobile-web-app-title" content="Richierich" />
  <link rel="apple-touch-icon" href="{{ asset('assets/dress_apple-touch-icon.png') }}?v=3" />
  <link rel="stylesheet" href="{{ asset('assets/dress_styles.css') }}?v=100" />
  @include('partials.dress-config')
  <script src="{{ asset('assets/dress_loader.js') }}?v=9"></script>
  @stack('head')
</head>
<body class="@yield('body_class')" data-dress-page="@yield('dress_page', 'home')">
  <header class="site-header">
    <div class="header-flora" aria-hidden="true">
      <svg class="header-flora-left" viewBox="0 0 180 200" fill="none">
        <path d="M18 28c18 6 28 22 24 42-14-4-28-16-24-42Z" fill="#e6d3bc"/>
        <path d="M34 18c22 2 34 20 30 42-16-8-32-16-30-42Z" fill="#efe2d2"/>
        <path d="M8 62c16 14 22 34 12 52-16-8-26-28-12-52Z" fill="#e4d0b6"/>
        <path d="M46 58c20 8 28 28 16 48-14-10-28-22-16-48Z" fill="#f3e7da"/>
        <path d="M22 96c14 16 16 36 4 52-12-12-20-30-4-52Z" fill="#e8d6c2"/>
      </svg>
      <svg class="header-flora-right" viewBox="0 0 180 200" fill="none">
        <path d="M162 28c-18 6-28 22-24 42 14-4 28-16 24-42Z" fill="#e6d3bc"/>
        <path d="M146 18c-22 2-34 20-30 42 16-8 32-16 30-42Z" fill="#efe2d2"/>
        <path d="M172 62c-16 14-22 34-12 52 16-8 26-28 12-52Z" fill="#e4d0b6"/>
        <path d="M134 58c-20 8-28 28-16 48 14-10 28-22 16-48Z" fill="#f3e7da"/>
        <path d="M158 96c-14 16-16 36-4 52 12-12 20-30 4-52Z" fill="#e8d6c2"/>
      </svg>
    </div>
    <div class="container navbar">
      <a class="logo" href="{{ route('home') }}"><img src="{{ asset('assets/dress_logo.png') }}?v=4" alt="Richierich" /></a>
      <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <nav class="nav-menu" aria-label="Primary">
        <div class="nav-links">
          <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
          <a href="{{ route('shop.products') }}" class="{{ request()->routeIs('shop.products') || request()->routeIs('shop.show') ? 'active' : '' }}">Shop</a>
          <a href="{{ route('shop.contact') }}" class="{{ request()->routeIs('shop.contact') ? 'active' : '' }}">Contact</a>
        </div>
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
        <a class="logo" href="{{ route('home') }}"><img src="{{ asset('assets/dress_logo.png') }}?v=4" alt="Richierich" /></a>
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
          @php $wa = \App\Models\Store::siteWhatsapp(); @endphp
          @if(($settings->show_whatsapp ?? true) && $wa)
            <li><a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">WhatsApp</a></li>
            <li><a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">+{{ $wa }}</a></li>
          @endif
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">Copyright © {{ date('Y') }} Richierich. All Rights Reserved. Designed and developed by Azores Interactive.</div>
    </div>
  </footer>

  <script src="{{ asset('assets/dress_main.js') }}?v=80"></script>
  <script src="{{ asset('assets/dress_cart.js') }}?v=8"></script>
  @stack('scripts')
  <script src="{{ asset('assets/dress_pwa.js') }}?v=3"></script>
  <script src="{{ asset('assets/azores_share.js') }}?v=4"></script>
</body>
</html>
