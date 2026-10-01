@extends('layouts.shop')

@section('dress_page', 'more')
@section('title', 'Sets — Richierich')

@section('content')
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Sets</span>
    </div>
    <h1>Ready look sets</h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="combo-grid" id="combos-page-grid"></div>
  </div>
</section>
@endsection
