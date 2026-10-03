@extends('layouts.admin')

@section('title', 'Products')
@section('heading', 'Products')
@section('subheading', 'Create, edit and manage catalogue items')
@section('actions')
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add product</a>
@endsection

@section('content')
<div class="card">
  <form method="GET" action="{{ route('admin.products.index') }}" class="toolbar">
    <div class="field">
      <label for="q">Search</label>
      <input id="q" type="search" name="q" value="{{ request('q') }}" placeholder="Name, SKU, designer" />
    </div>
    <div class="field">
      <label for="category_id">Category</label>
      <select id="category_id" name="category_id">
        <option value="">All</option>
        @foreach($categories as $category)
          <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">All</option>
        @foreach(['active','draft','archived'] as $st)
          <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </div>
    <div class="field">
      <button type="submit" class="btn btn-secondary btn-block">Filter</button>
    </div>
  </form>

  <div class="table-wrap desktop-only">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td>
              @if($product->primary_image_url)
                <img class="thumb" src="{{ $product->primary_image_url }}" alt="" />
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td>
              <strong>{{ $product->name }}</strong>
              @if($product->sku)<div class="muted" style="font-size:0.8rem;">{{ $product->sku }}</div>@endif
              @if($product->admin_note)
                <div class="muted" style="font-size:0.78rem;margin-top:0.25rem;">Note: {{ \Illuminate\Support\Str::limit($product->admin_note, 80) }}</div>
              @endif
            </td>
            <td>{{ $product->category?->name ?? '—' }}</td>
            <td>
              @if($product->show_price)
                ₹{{ number_format((float) $product->price, 2) }}
              @else
                <span class="muted">Hidden</span>
              @endif
            </td>
            <td>
              @if($product->stock === null)
                <span class="badge badge-muted">Not set</span>
              @elseif($product->stock < 1)
                <span class="badge badge-muted">Out · 0</span>
              @elseif($product->stock_threshold !== null && $product->stock <= $product->stock_threshold)
                <span class="badge badge-warn">Low · {{ $product->stock }}</span>
              @else
                <span class="badge badge-success">{{ $product->stock }}</span>
              @endif
            </td>
            <td>
              <span class="badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'draft' ? 'badge-warn' : 'badge-muted') }}">
                {{ $product->status }}
              </span>
            </td>
            <td class="actions">
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.products.edit', $product) }}">Edit</a>
              <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No products yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mobile-list">
    @forelse($products as $product)
      <article class="mobile-item">
        <div class="mobile-item-top">
          @if($product->primary_image_url)
            <img class="thumb" src="{{ $product->primary_image_url }}" alt="" />
          @endif
          <div class="mobile-item-body">
            <strong>{{ $product->name }}</strong>
            <span class="badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'draft' ? 'badge-warn' : 'badge-muted') }}">{{ $product->status }}</span>
            <div class="mobile-item-meta">
              <span>{{ $product->category?->name ?? 'No category' }}</span>
              <span>
                @if($product->show_price)
                  ₹{{ number_format((float) $product->price, 2) }}
                @else
                  Price hidden
                @endif
              </span>
              <span>
                @if($product->stock === null)
                  Stock not set
                @elseif($product->stock < 1)
                  Out of stock
                @else
                  Stock {{ $product->stock }}
                @endif
              </span>
            </div>
            @if($product->admin_note)
              <div class="muted" style="font-size:0.78rem;margin-top:0.35rem;">Note: {{ \Illuminate\Support\Str::limit($product->admin_note, 80) }}</div>
            @endif
          </div>
        </div>
        <div class="mobile-item-actions">
          <a class="btn btn-secondary btn-sm" href="{{ route('admin.products.edit', $product) }}">Edit</a>
          <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </article>
    @empty
      <p class="muted">No products yet.</p>
    @endforelse
  </div>

  <div class="pagination">{{ $products->withQueryString()->links('admin.partials.pagination') }}</div>
</div>
@endsection
