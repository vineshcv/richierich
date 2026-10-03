@extends('layouts.shop')

@section('dress_page', 'products')
@section('title', 'Products — Richierich')
@section('meta_description', 'Browse categories, products and combos. Enquire on WhatsApp.')

@push('head')
  @include('partials.playfair')
@endpush

@section('content')
<section class="shop-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>›</span><span>Products</span>
    </div>
    <h1 id="catalog-title">All products</h1>
    <div class="filters shop-filters" id="catalog-filters"></div>
  </div>
</section>

<section class="section shop-catalog">
  <div class="container">
    <div class="shop-grid" id="catalog-grid"></div>
  </div>
</section>
@endsection
