@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Overview of your Richierich storefront')

@section('content')
<div class="stats">
  <div class="stat">
    <span>Products</span>
    <strong>{{ $productCount }}</strong>
    <a href="{{ route('admin.products.index') }}">Manage products →</a>
  </div>
  <div class="stat">
    <span>Categories</span>
    <strong>{{ $categoryCount }}</strong>
    <a href="{{ route('admin.categories.index') }}">Manage categories →</a>
  </div>
  <div class="stat">
    <span>Orders</span>
    <strong>{{ $orderCount }}</strong>
    <a href="{{ route('admin.orders.index') }}">View orders →</a>
  </div>
  <div class="stat">
    <span>Banners</span>
    <strong>{{ $bannerCount }} / {{ $bannerMax }}</strong>
    <a href="{{ route('admin.banners.index') }}">Manage banners →</a>
  </div>
</div>

<div class="card">
  <h2 class="card-title">Quick actions</h2>
  <div class="actions">
    <a class="btn btn-primary" href="{{ route('admin.products.create') }}">Add product</a>
    <a class="btn btn-secondary" href="{{ route('admin.categories.create') }}">Add category</a>
    <a class="btn btn-secondary" href="{{ route('admin.banners.index') }}">Upload banner</a>
    <a class="btn btn-ghost" href="{{ route('home') }}" target="_blank" rel="noopener">Open store</a>
  </div>
</div>
@endsection
