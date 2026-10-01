@extends('layouts.shop')

@section('dress_page', 'more')
@section('title', 'Festive edit — Richierich')

@section('content')
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Festive edit</span>
    </div>
    <h1>Festive edit packages</h1>
    <p>Limited-time packages. Choose home delivery or Pack & Pickup when you enquire or share.</p>
  </div>
</section>

<section class="promo-banner">
  <img src="{{ asset('assets/b2.png') }}" alt="Festive edit at Richierich" />
</section>

<section class="section">
  <div class="container">
    <div class="combo-grid" id="season-page-grid"></div>
  </div>
</section>
@endsection
