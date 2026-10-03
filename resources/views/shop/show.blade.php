@extends('layouts.shop')

@section('dress_page', 'detail')
@section('title', $product->name.' — Richierich')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: $product->name), 160))

@push('head')
  @include('partials.playfair')
@endpush

@section('content')
<section class="page-hero detail-crumb">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('shop.products') }}">Shop</a><span>/</span>
      @if($product->category)
        <a href="{{ route('shop.products') }}?cat={{ $product->category->slug }}">{{ $product->category->name }}</a><span>/</span>
      @endif
      <span>{{ $product->name }}</span>
    </div>
  </div>
</section>

<section class="section detail-section">
  <div class="container">
    <div id="detail-root" data-product-id="{{ $product->slug }}"></div>
  </div>
</section>

<div id="related-section">
  <section class="section home-cat-section">
    <div class="container">
      <div class="section-head section-head-compact">
        <div>
          <p class="eyebrow">More to love</p>
          <h2>Related Products</h2>
        </div>
        <a class="btn btn-primary home-view-all" href="{{ route('shop.products') }}{{ $product->category ? '?cat='.$product->category->slug : '' }}">View all <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg></a>
      </div>
      <div class="shop-grid" id="related-grid"></div>
    </div>
  </section>
</div>
@endsection
