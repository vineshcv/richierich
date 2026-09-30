@extends('layouts.shop')

@section('dress_page', 'home')
@section('title', ($settings->store_name ?? 'Richie Rich').' Boutique — Ethnic & western dresses · WhatsApp enquire')

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
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?: (($settings->store_name ?? 'Richie Rich').' banner') }}" />
          </a>
        @empty
          <a class="banner-card" href="{{ route('shop.products') }}"><img src="{{ asset('assets/b1.png') }}" alt="Banner" /></a>
          <a class="banner-card" href="{{ route('shop.products') }}"><img src="{{ asset('assets/b2.png') }}" alt="Banner" /></a>
          <a class="banner-card" href="{{ route('shop.products') }}"><img src="{{ asset('assets/b3.png') }}" alt="Banner" /></a>
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
      <button type="button" class="category-carousel-btn prev" id="category-prev" aria-label="Previous categories">‹</button>
      <div class="category-grid" id="category-grid"></div>
      <button type="button" class="category-carousel-btn next" id="category-next" aria-label="Next categories">›</button>
    </div>
  </div>
</section>

<div id="home-category-sections"></div>
@endsection
