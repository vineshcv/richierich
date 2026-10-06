@extends('layouts.admin')

@php $isEdit = $user->exists; @endphp

@section('title', $isEdit ? 'Edit user' : 'Add user')
@section('heading', $isEdit ? 'Edit user' : 'Add user')
@section('subheading', 'Username, email, password, status, and phone. A store admin is limited to one store.')
@section('actions')
  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
<div class="card">
  <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="field">
        <label for="username">Username *</label>
        <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" required autocomplete="off" />
        @error('username')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="email">Email *</label>
        @php
          $storedEmail = (string) ($user->email ?? '');
          $emailValue = old('email', str_ends_with($storedEmail, '@staff.richierich.local') ? '' : $storedEmail);
        @endphp
        <input id="email" type="email" name="email" value="{{ $emailValue }}" required autocomplete="email" />
        @error('email')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="password">Password {{ $isEdit ? '' : '*' }}</label>
        <input id="password" type="password" name="password" {{ $isEdit ? '' : 'required' }} autocomplete="new-password" />
        @if($isEdit)<p class="muted" style="font-size:0.8rem;margin:0.35rem 0 0;">Leave blank to keep the current password.</p>@endif
        @error('password')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="phone">Phone number</label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" />
        @error('phone')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="status">Status *</label>
        <select id="status" name="status">
          <option value="active" @selected(old('status', $user->status ?? 'active') === 'active')>Active</option>
          <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
        </select>
        @error('status')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="role">Role *</label>
        <select id="role" name="role">
          <option value="superadmin" @selected(old('role', $user->role) === 'superadmin')>Superadmin</option>
          <option value="store_admin" @selected(old('role', $user->role ?: 'store_admin') === 'store_admin')>Store admin</option>
        </select>
        @error('role')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field" id="store-field">
        <label for="store_id">Store *</label>
        <select id="store_id" name="store_id">
          <option value="">Choose a store</option>
          @foreach($stores as $store)
            <option value="{{ $store->id }}" @selected((string) old('store_id', $user->store_id) === (string) $store->id)>{{ $store->name }}</option>
          @endforeach
        </select>
        @error('store_id')<div class="error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div style="margin-top:1.25rem;">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update user' : 'Create user' }}</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
  var role = document.getElementById('role');
  var storeField = document.getElementById('store-field');
  var store = document.getElementById('store_id');
  function sync() {
    var isStore = role && role.value === 'store_admin';
    if (storeField) storeField.hidden = !isStore;
    if (store) store.required = !!isStore;
  }
  role?.addEventListener('change', sync);
  sync();
})();
</script>
@endpush
