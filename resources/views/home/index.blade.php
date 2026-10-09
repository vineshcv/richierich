@extends('layouts.shop')

@section('dress_page', 'home')
@section('title', 'Richierich — Ethnic & western dresses · WhatsApp enquire')

@php $leadBanner = ($banners ?? collect())->first(); @endphp
@push('preload')
  @if($leadBanner)
    <link rel="preload" as="image" href="{{ $leadBanner->image_url }}" fetchpriority="high" />
  @else
    <link rel="preload" as="image" href="{{ asset('assets/hero-style.jpg') }}" fetchpriority="high" />
  @endif
@endpush
@push('head')
  @include('partials.playfair')
@endpush

@section('content')
<section class="dress-top">
  <div class="container">
    <form class="dress-search" action="{{ route('shop.products') }}" method="get" role="search">
      <svg class="dress-search-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M20 20l-3.5-3.5"/></svg>
      <input type="search" name="q" placeholder="Search sarees, kurtis, sets and more" aria-label="Search products" />
    </form>

    <div class="banner-wrap">
      <div class="banner-rail" id="banner-rail" tabindex="0" aria-label="Offers">
        @forelse($banners as $banner)
          <a class="banner-card" href="{{ $banner->link_url ?: route('shop.products') }}">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?: 'Richierich banner' }}" width="1100" height="655" @if($loop->first) fetchpriority="high" @else loading="lazy" @endif />
          </a>
        @empty
          <a class="banner-card" href="{{ route('shop.products') }}"><img src="{{ asset('assets/hero-style.jpg') }}" alt="Celebrate every moment in style" width="1100" height="655" fetchpriority="high" /></a>
          <a class="banner-card" href="{{ route('shop.products') }}"><img src="{{ asset('assets/hero-sarees.jpg') }}" alt="Elegant sarees" width="1100" height="655" loading="lazy" /></a>
          <a class="banner-card" href="{{ route('shop.products') }}"><img src="{{ asset('assets/hero-kurtis.jpg') }}" alt="Trendy kurtis and sets" width="1100" height="655" loading="lazy" /></a>
        @endforelse
      </div>
      <div class="banner-dots" id="banner-dots" aria-hidden="true"></div>
    </div>
  </div>
</section>

<section class="section" id="categories">
  <div class="container">
    <div class="section-head centered">
      <div>
        <p class="eyebrow">Shop by category</p>
        <h2>Categories</h2>
       
      </div>
    </div>
    <div class="category-carousel">
      <button type="button" class="category-carousel-btn prev" id="category-prev" aria-label="Previous categories" hidden>‹</button>
      <div class="category-grid" id="category-grid"></div>
      <button type="button" class="category-carousel-btn next" id="category-next" aria-label="Next categories" hidden>›</button>
    </div>
  </div>
</section>

<div id="home-category-sections"></div>
@endsection
