@extends('layouts.admin')

@section('title', 'Settings')
@section('heading', 'Settings')
@section('subheading', 'Storefront button visibility. WhatsApp numbers are set on each store.')

@section('content')
<div class="card">
  <form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <h2 class="card-title">Storefront buttons</h2>
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
