@extends('layouts.shop')

@section('dress_page', 'detail')
@section('title', $product->name.' — '.($settings->store_name ?? 'Richie Rich').' Boutique')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->description ?: $product->name), 160))

@push('head')
  <meta property="og:title" content="{{ $product->name }}" />
  <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description ?: $product->name), 160) }}" />
  @if($product->primary_image_url)
    <meta property="og:image" content="{{ $product->primary_image_url }}" />
  @endif
@endpush

@section('content')
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span>
      <a href="{{ route('shop.products') }}">Shop</a><span>/</span>
      <span>{{ $product->name }}</span>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div id="detail-root" data-product-id="{{ $product->slug }}"></div>
  </div>
</section>

<section class="section alt">
  <div class="container">
    <div class="section-head">
      <div>
        <p class="eyebrow">More to love</p>
        <h2>Related products</h2>
      </div>
    </div>
    <div class="shop-grid" id="related-grid"></div>
  </div>
</section>
@endsection
