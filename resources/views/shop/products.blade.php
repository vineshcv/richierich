@extends('layouts.shop')

@section('dress_page', 'products')
@section('title', 'Products — '.($settings->store_name ?? 'Richie Rich').' Boutique')
@section('meta_description', 'Browse categories, products and combos. Enquire on WhatsApp.')

@section('content')
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Products</span>
    </div>
    <h1 id="catalog-title">All products</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filters" id="catalog-filters"></div>
    <div class="shop-grid" id="catalog-grid"></div>
  </div>
</section>
@endsection
