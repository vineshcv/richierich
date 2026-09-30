@extends('layouts.shop')

@section('dress_page', 'more')
@section('title', 'Custom order — '.($settings->store_name ?? 'Richie Rich').' Boutique')

@section('content')
@php $wa = preg_replace('/\D+/', '', $settings->whatsapp_number ?? ''); @endphp
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Custom order</span>
    </div>
    <h1>Custom / wholesale order</h1>
    <p>For offices, hostels, canteens and shops. Submit the form — it opens WhatsApp with your list filled in.</p>
  </div>
</section>

<section class="section">
  <div class="container bulk-grid">
    <div class="info-card">
      <h2>How bulk works</h2>
      <p>Share items and approximate quantities. We reply with pack sizes, brands in stock and a clear quote.</p>
      <div class="info-row">
        <h3>Best for</h3>
        <p>Offices, hostels, mess halls, small shops, monthly family stock-ups.</p>
      </div>
      <div class="info-row">
        <h3>WhatsApp</h3>
        <p>
          @if($wa)
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">WhatsApp</a><br />
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">+{{ $wa }}</a>
          @endif
        </p>
      </div>
      <div class="info-row">
        <h3>Tip</h3>
        <p>Mention delivery area and whether you need weekly or monthly supply.</p>
      </div>
      <a class="btn btn-outline" href="{{ route('shop.combos') }}">See look sets</a>
    </div>

    <div class="form-card">
      <h2>Bulk enquiry form</h2>
      <p>All fields go into your WhatsApp message — nothing is stored on the site.</p>
      <form data-wa-form="Hi {{ $settings->store_name ?? 'Richie Rich' }} Boutique, BULK ORDER enquiry:">
        <div class="form-grid">
          <div class="field">
            <label for="name">Name / business</label>
            <input id="name" name="Name" type="text" required placeholder="Your name or firm" />
          </div>
          <div class="field">
            <label for="phone">WhatsApp number</label>
            <input id="phone" name="Phone" type="tel" required placeholder="10-digit mobile" />
          </div>
          <div class="field">
            <label for="type">Order type</label>
            <select id="type" name="Order type">
              <option value="Home monthly bulk">Home monthly bulk</option>
              <option value="Office / staff pantry">Office / staff pantry</option>
              <option value="Hostel / mess">Hostel / mess</option>
              <option value="Shop restock">Shop restock</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div class="field">
            <label for="area">Delivery area</label>
            <input id="area" name="Area" type="text" placeholder="Your locality" />
          </div>
          <div class="field full">
            <label for="items">Item list &amp; quantities</label>
            <textarea id="items" name="Items" required placeholder="Example:&#10;Silk saree × 2&#10;Kurti set M × 5"></textarea>
          </div>
          <div class="field full">
            <label for="notes">Notes</label>
            <textarea id="notes" name="Notes" placeholder="Brand preferences, urgency, budget..."></textarea>
          </div>
        </div>
        <div style="margin-top: 1rem">
          <button class="btn btn-primary" type="submit">Submit on WhatsApp</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
