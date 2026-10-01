@extends('layouts.admin')

@section('title', 'Stores')
@section('heading', 'Stores')
@section('subheading', 'Each store keeps its own products, categories, banners, and orders')
@section('actions')
  <a href="{{ route('admin.stores.create') }}" class="btn btn-primary">Add store</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap desktop-only">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>Phone</th>
          <th>WhatsApp</th>
          <th>PIN</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($stores as $store)
          <tr>
            <td><strong>{{ $store->name }}</strong></td>
            <td>{{ $store->location ?: '—' }}</td>
            <td>{{ $store->phone ?: '—' }}</td>
            <td>{{ $store->whatsapp_number ?: '—' }}</td>
            <td>{{ $store->pin ?: '—' }}</td>
            <td class="actions">
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.stores.edit', $store) }}">Edit</a>
              <form method="POST" action="{{ route('admin.stores.destroy', $store) }}" onsubmit="return confirm('Delete this store?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No stores yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mobile-list">
    @forelse($stores as $store)
      <article class="mobile-item">
        <div class="mobile-item-body">
          <strong>{{ $store->name }}</strong>
          <div class="mobile-item-meta">
            <span>{{ $store->location ?: 'Location not set' }}</span>
            <span>{{ $store->phone ?: 'No phone' }}</span>
            <span>WhatsApp {{ $store->whatsapp_number ?: '—' }}</span>
            <span>PIN {{ $store->pin ?: '—' }}</span>
          </div>
        </div>
        <div class="mobile-item-actions">
          <a class="btn btn-secondary btn-sm" href="{{ route('admin.stores.edit', $store) }}">Edit</a>
          <form method="POST" action="{{ route('admin.stores.destroy', $store) }}" onsubmit="return confirm('Delete this store?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </article>
    @empty
      <p class="muted">No stores yet.</p>
    @endforelse
  </div>
</div>
@endsection
