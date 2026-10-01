@extends('layouts.shop')

@section('dress_page', 'products')
@section('title', 'Products — Richierich')
@section('meta_description', 'Browse categories, products and combos. Enquire on WhatsApp.')

@push('head')
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet" />
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
