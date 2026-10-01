@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Categories')
@section('subheading', 'Shared by every store. A category name can only be created once.')
@section('actions')
  <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add category</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap desktop-only">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Slug</th>
          <th>Order</th>
          <th>Active</th>
          <th>Products</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $category)
          <tr>
            <td>
              @if($category->image_url)
                <img class="thumb" src="{{ $category->image_url }}" alt="" />
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td><strong>{{ $category->name }}</strong></td>
            <td class="muted">{{ $category->slug }}</td>
            <td>{{ $category->sort_order }}</td>
            <td>
              <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-muted' }}">
                {{ $category->is_active ? 'Active' : 'Off' }}
              </span>
            </td>
            <td>{{ $category->products()->count() }}</td>
            <td class="actions">
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
              <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No categories yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mobile-list">
    @forelse($categories as $category)
      <article class="mobile-item">
        <div class="mobile-item-top">
          @if($category->image_url)
            <img class="thumb" src="{{ $category->image_url }}" alt="" />
          @endif
          <div class="mobile-item-body">
            <strong>{{ $category->name }}</strong>
            <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-muted' }}">
              {{ $category->is_active ? 'Active' : 'Off' }}
            </span>
            <div class="mobile-item-meta">
              <span>{{ $category->slug }}</span>
              <span>{{ $category->products()->count() }} products</span>
              <span>Order {{ $category->sort_order }}</span>
            </div>
          </div>
        </div>
        <div class="mobile-item-actions">
          <a class="btn btn-secondary btn-sm" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
          <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </article>
    @empty
      <p class="muted">No categories yet.</p>
    @endforelse
  </div>
</div>
@endsection
