@extends('layouts.admin')

@php $isEdit = $store->exists; @endphp

@section('title', $isEdit ? 'Edit store' : 'Add store')
@section('heading', $isEdit ? 'Edit store' : 'Add store')
@section('subheading', 'Store name, location, address, WhatsApp, and PIN. Customers never see the store.')
@section('actions')
  <a href="{{ route('admin.stores.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
<div class="card">
  <form method="POST" action="{{ $isEdit ? route('admin.stores.update', $store) : route('admin.stores.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-grid">
      <div class="field">
        <label for="name">Store name *</label>
        <input id="name" type="text" name="name" value="{{ old('name', $store->name) }}" required />
        @error('name')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="location">Location *</label>
        <input id="location" type="text" name="location" value="{{ old('location', $store->location) }}" placeholder="City or area" required />
        @error('location')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field full">
        <label for="address">Address *</label>
        <input id="address" type="text" name="address" value="{{ old('address', $store->address) }}" placeholder="Street address" required />
        @error('address')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="phone">Phone number</label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone', $store->phone) }}" placeholder="Optional" />
        @error('phone')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="whatsapp_number">WhatsApp number *</label>
        <input id="whatsapp_number" type="text" name="whatsapp_number" inputmode="numeric" value="{{ old('whatsapp_number', $store->whatsapp_number) }}" placeholder="919567779354" required />
        <p class="muted" style="font-size:0.8rem;margin:0.35rem 0 0;">Digits with country code. Product enquiries for this store open this chat.</p>
        @error('whatsapp_number')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label for="pin">PIN *</label>
        <input id="pin" type="text" name="pin" inputmode="numeric" maxlength="6" value="{{ old('pin', $store->pin) }}" placeholder="6-digit PIN" required />
        @error('pin')<div class="error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div style="margin-top:1.25rem;">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update store' : 'Create store' }}</button>
    </div>
  </form>
</div>
@endsection
