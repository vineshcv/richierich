<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#111827" />
  <title>@yield('title', 'Admin') — Richierich</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}?v=3" sizes="any" />
  <link rel="icon" href="{{ asset('assets/dress_icon-192.png') }}?v=3" type="image/png" />
  <link rel="stylesheet" href="{{ asset('assets/admin.css') }}?v=8" />
  @stack('head')
</head>
<body>
@php
  $ico = [
    'dash' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 10.5L12 4l8 6.5V20a1 1 0 01-1 1h-5v-6H10v6H5a1 1 0 01-1-1v-9.5z"/></svg>',
    'box' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 5 9-5M12 13v8"/></svg>',
    'grid' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/></svg>',
    'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2"/><circle cx="9" cy="10" r="1.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 16l4-4 3 3 3-2 3 3"/></svg>',
    'gear' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 00.3 1.9l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.9-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.9.3l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1.7 1.7 0 00.3-1.9 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1 1.7 1.7 0 00-.3-1.9l-.1-.1a2 2 0 112.8-2.8l.1.1a1.7 1.7 0 001.9.3H9a1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.9-.3l.1-.1a2 2 0 112.8 2.8l-.1.1a1.7 1.7 0 00-.3 1.9V9c.2.6.8 1 1.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z"/></svg>',
    'store' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 9l1.5-5h13L20 9M4 9h16v10a1 1 0 01-1 1H5a1 1 0 01-1-1V9z"/><path stroke-linecap="round" d="M9 14h6"/></svg>',
    'users' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19v-1a3 3 0 00-3-3H7a3 3 0 00-3 3v1"/><circle cx="10" cy="8" r="3"/><path stroke-linecap="round" d="M20 19v-1a3 3 0 00-2.2-2.9M16 5.1a3 3 0 010 5.8"/></svg>',
    'external' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5M19 5l-8 8"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 13v5a1 1 0 01-1 1H6a1 1 0 01-1-1V8a1 1 0 011-1h5"/></svg>',
    'orders' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path stroke-linecap="round" d="M14 3v5h5M8 13h8M8 17h5"/></svg>',
  ];
@endphp

@unless(request()->routeIs('admin.login'))
  <div class="admin-shell">
    <div class="admin-backdrop" id="admin-backdrop" hidden></div>

    <aside class="admin-sidebar" id="admin-sidebar" aria-label="Admin menu">
      <div class="admin-brand">
        <img src="{{ asset('assets/dress_logo.png') }}?v=4" alt="Richierich" />
        <div>
          <strong>Richierich</strong>
          <span>Admin panel</span>
        </div>
      </div>

      <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">{!! $ico['dash'] !!}<span>Dashboard</span></a>
        @if(auth()->user()?->role === 'superadmin')
          <a href="{{ route('admin.stores.index') }}" class="{{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">{!! $ico['store'] !!}<span>Stores</span></a>
          <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">{!! $ico['users'] !!}<span>Users</span></a>
        @endif
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">{!! $ico['box'] !!}<span>Products</span></a>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">{!! $ico['grid'] !!}<span>Categories</span></a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">{!! $ico['orders'] !!}<span>Orders</span></a>
        <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">{!! $ico['image'] !!}<span>Banners</span></a>
        @if(auth()->user()?->role === 'superadmin')
          <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">{!! $ico['gear'] !!}<span>Settings</span></a>
        @endif
        <a href="{{ route('home') }}" target="_blank" rel="noopener">{!! $ico['external'] !!}<span>View website</span></a>
      </nav>

      <div class="admin-sidebar-foot">
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button type="submit" class="btn btn-secondary">Logout</button>
        </form>
      </div>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <button type="button" class="admin-menu-btn" id="admin-menu-btn" aria-label="Open menu" aria-expanded="false" aria-controls="admin-sidebar">
          <span></span>
        </button>
        <div class="admin-topbar-title">@yield('heading', 'Admin')</div>
        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" target="_blank" rel="noopener">Store</a>
      </header>

      <div class="admin-content">
        @if($canSwitchStore ?? false)
          <form class="admin-storebar" method="POST" action="{{ route('admin.stores.switch') }}">
            @csrf
            <label for="admin-store">Store</label>
            <select id="admin-store" name="store_id" onchange="this.form.submit()">
              @foreach($adminStores as $storeOption)
                <option value="{{ $storeOption->id }}" @selected(($currentStore->id ?? null) === $storeOption->id)>{{ $storeOption->name }}{{ $storeOption->location ? ' · '.$storeOption->location : '' }}</option>
              @endforeach
            </select>
          </form>
        @elseif($currentStore ?? null)
          <div class="admin-storebar">
            <span>Store</span>
            <strong>{{ $currentStore->name }}</strong>
          </div>
        @endif

        <div class="admin-page-head">
          <div>
            <h1>@yield('heading', 'Admin')</h1>
            @hasSection('subheading')
              <p class="sub">@yield('subheading')</p>
            @endif
          </div>
          <div class="admin-page-actions">@yield('actions')</div>
        </div>

        @if(session('success'))
          <div class="alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-error">
            <ul style="margin:0;padding-left:1.1rem;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </div>
    </div>
  </div>

  <nav class="admin-bottom-nav" aria-label="Admin shortcuts">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">{!! $ico['dash'] !!}<span>Home</span></a>
    @if(auth()->user()?->role === 'superadmin')
      <a href="{{ route('admin.stores.index') }}" class="{{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">{!! $ico['store'] !!}<span>Stores</span></a>
    @endif
    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">{!! $ico['box'] !!}<span>Products</span></a>
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">{!! $ico['orders'] !!}<span>Orders</span></a>
    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">{!! $ico['grid'] !!}<span>Cats</span></a>
  </nav>

  <script>
    (function () {
      var btn = document.getElementById('admin-menu-btn');
      var backdrop = document.getElementById('admin-backdrop');
      function setOpen(open) {
        document.documentElement.classList.toggle('admin-nav-open', open);
        if (btn) {
          btn.setAttribute('aria-expanded', open ? 'true' : 'false');
          btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        }
        if (backdrop) backdrop.hidden = !open;
      }
      btn && btn.addEventListener('click', function () {
        setOpen(!document.documentElement.classList.contains('admin-nav-open'));
      });
      backdrop && backdrop.addEventListener('click', function () { setOpen(false); });
      document.querySelectorAll('.admin-nav a').forEach(function (link) {
        link.addEventListener('click', function () { setOpen(false); });
      });
      window.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setOpen(false);
      });
    })();
  </script>
@else
  @yield('content')
@endunless
@stack('scripts')
</body>
</html>
