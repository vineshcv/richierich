@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Users')
@section('subheading', 'Superadmin can open every store. A store admin sees only the store you assign.')
@section('actions')
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
@endsection

@section('content')
<div class="card">
  <div class="table-wrap desktop-only">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Username</th>
          <th>Role</th>
          <th>Store</th>
          <th>Phone</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td><strong>{{ $user->username }}</strong></td>
            <td>{{ $user->role === 'superadmin' ? 'Superadmin' : 'Store admin' }}</td>
            <td>{{ $user->store?->name ?: 'All stores' }}</td>
            <td>{{ $user->phone ?: '—' }}</td>
            <td>
              <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($user->status) }}</span>
            </td>
            <td class="actions">
              <a class="btn btn-secondary btn-sm" href="{{ route('admin.users.edit', $user) }}">Edit</a>
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No users yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mobile-list">
    @forelse($users as $user)
      <article class="mobile-item">
        <div class="mobile-item-body">
          <strong>{{ $user->username }}</strong>
          <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($user->status) }}</span>
          <div class="mobile-item-meta">
            <span>{{ $user->role === 'superadmin' ? 'Superadmin' : 'Store admin' }}</span>
            <span>{{ $user->store?->name ?: 'All stores' }}</span>
            <span>{{ $user->phone ?: 'No phone' }}</span>
          </div>
        </div>
        <div class="mobile-item-actions">
          <a class="btn btn-secondary btn-sm" href="{{ route('admin.users.edit', $user) }}">Edit</a>
          <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </article>
    @empty
      <p class="muted">No users yet.</p>
    @endforelse
  </div>
</div>
@endsection
