<!DOCTYPE html>
<html lang="en" @if(request()->routeIs('home')) data-loader="1" @endif>
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @stack('preload')
  @php
    $shareTitle = trim($__env->yieldContent('title'));
    if ($shareTitle === '') {
        $shareTitle = e('Richierich — Ethnic & western dresses · WhatsApp enquire');
    }
    $shareDescription = trim(preg_replace('/\s+/', ' ', strip_tags($__env->yieldContent('meta_description'))));
    if ($shareDescription === '') {
        $shareDescription = e('Shop sarees, kurtis, lehengas and western dresses. Enquire on WhatsApp.');
    }
    $shareUrl = url()->current();
    $shareImage = asset('assets/dress_og.jpg');
  @endphp
  <title>{!! $shareTitle !!}</title>
  <meta name="description" content="{!! $shareDescription !!}" />
  <link rel="canonical" href="{{ $shareUrl }}" />
  <meta property="og:locale" content="en_IN" />
  <meta property="og:site_name" content="Richierich" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ $shareUrl }}" />
  <meta property="og:title" content="{!! $shareTitle !!}" />
  <meta property="og:description" content="{!! $shareDescription !!}" />
  <meta property="og:image" content="{{ $shareImage }}" />
  @if(str_starts_with($shareImage, 'https://'))
    <meta property="og:image:secure_url" content="{{ $shareImage }}" />
  @endif
  <meta property="og:image:type" content="image/jpeg" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="Richierich" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="{!! $shareTitle !!}" />
  <meta name="twitter:description" content="{!! $shareDescription !!}" />
  <meta name="twitter:image" content="{{ $shareImage }}" />
  <meta name="twitter:image:alt" content="Richierich" />
  <link rel="icon" href="{{ asset('favicon.ico') }}?v=3" sizes="any" />
  <link rel="icon" href="{{ asset('assets/dress_icon-192.png') }}?v=3" type="image/png" />
  <link rel="apple-touch-icon" href="{{ asset('assets/dress_apple-touch-icon.png') }}?v=3" />
  <link rel="stylesheet" href="{{ asset('assets/dress_styles.css') }}?v=130" />
  @include('partials.dress-config')
  <script src="{{ asset('assets/dress_loader.js') }}?v=12"></script>
  @stack('head')
</head>
<body class="@yield('body_class')" data-dress-page="@yield('dress_page', 'home')">
  <header class="site-header">
    <div class="container navbar">
      <a class="logo" href="{{ route('home') }}"><img src="{{ asset('assets/dress_logo.webp') }}?v=7" alt="Richierich" width="776" height="160" /></a>
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
        <a class="logo" href="{{ route('home') }}"><img src="{{ asset('assets/dress_logo.webp') }}?v=7" alt="Richierich" width="776" height="160" /></a>
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
          <li>Ground Floor, Selex Mall, Anjangadi, Thrissur East, PIN 680005</li>
          @php $wa = \App\Models\Store::siteWhatsapp(); @endphp
          @if(($settings->show_whatsapp ?? true) && $wa)
            <li><a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">WhatsApp</a></li>
            <li><a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">+{{ $wa }}</a></li>
          @endif
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">
        <span>Copyright © {{ date('Y') }} Richierich. All Rights Reserved.</span>
        <a class="footer-credit" href="https://azoresinteractive.com/" target="_blank" rel="noopener noreferrer">Designed and developed by Azoresinteractive</a>
      </div>
    </div>
  </footer>

  <script src="{{ asset('assets/dress_main.js') }}?v=85"></script>
  <script src="{{ asset('assets/dress_cart.js') }}?v=9"></script>
  @stack('scripts')
  <script src="{{ asset('assets/azores_share.js') }}?v=4"></script>
</body>
</html>
