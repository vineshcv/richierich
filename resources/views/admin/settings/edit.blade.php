@extends('layouts.admin')

@section('title', 'Settings')
@section('heading', 'Store settings')
@section('subheading', 'WhatsApp number and storefront button visibility')

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="form-grid">
      <div class="field">
        <label for="store_name">Store name</label>
        <input id="store_name" type="text" name="store_name" value="{{ old('store_name', $settings->store_name) }}" />
      </div>
      <div class="field">
        <label for="whatsapp_number">WhatsApp number</label>
        <input id="whatsapp_number" type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" placeholder="919916399733" />
        <p class="muted" style="font-size:0.8rem;margin:0.35rem 0 0;">Digits only with country code, e.g. 919916399733</p>
      </div>
    </div>

    <h2 class="card-title" style="margin-top:1.4rem;">Storefront buttons</h2>
    @foreach([
      'show_share' => 'Show share button',
      'show_whatsapp' => 'Show WhatsApp',
      'show_cart' => 'Show cart',
      'show_view' => 'Show view button',
    ] as $key => $label)
      <div class="check-row">
        <input type="hidden" name="{{ $key }}" value="0" />
        <input id="{{ $key }}" type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $settings->$key)) />
        <label for="{{ $key }}">{{ $label }}</label>
      </div>
    @endforeach

    <div style="margin-top:1.25rem;">
      <button type="submit" class="btn btn-primary">Save settings</button>
    </div>
  </form>
</div>
@endsection
