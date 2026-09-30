@extends('layouts.shop')

@section('dress_page', 'contact')
@section('title', 'Contact — '.($settings->store_name ?? 'Richie Rich').' Boutique')
@section('meta_description', 'Contact '.$settings->store_name.'. Enquire on WhatsApp.')

@section('content')
@php $wa = preg_replace('/\D+/', '', $settings->whatsapp_number ?? ''); @endphp
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>/</span><span>Contact</span>
    </div>
    <h1>Contact {{ $settings->store_name ?? 'Richie Rich' }} Boutique</h1>
    <p>Message us on WhatsApp or send the form — it opens WhatsApp with your details.</p>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div class="info-card">
      <h2>Talk to us</h2>
      <p>Ask about sizes, fabrics or look sets. Ask on WhatsApp for sizes and availability.</p>
      <div class="info-row">
        <h3>WhatsApp</h3>
        <p>
          @if($wa)
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">WhatsApp</a><br />
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">+{{ $wa }}</a>
          @endif
        </p>
      </div>
      <a class="btn btn-primary" href="{{ route('shop.products') }}">Shop dresses</a>
    </div>

    <div class="form-card">
      <h2>Quick enquiry</h2>
      <p>Submit opens WhatsApp with your message filled in.</p>
      <form data-wa-form="Hi {{ $settings->store_name ?? 'Richie Rich' }} Boutique, enquiry from website:">
        <div class="form-grid">
          <div class="field">
            <label for="name">Name</label>
            <input id="name" name="Name" type="text" required placeholder="Your name" />
          </div>
          <div class="field">
            <label for="phone">WhatsApp number</label>
            <input id="phone" name="Phone" type="tel" required placeholder="10-digit mobile" />
          </div>
          <div class="field">
            <label for="interest">Interested in</label>
            <select id="interest" name="Interest">
              <option value="Saree">Saree</option>
              <option value="Lehengas">Lehengas</option>
              <option value="Western">Western</option>
              <option value="Kids">Kids</option>
              <option value="Look set">Look set</option>
              <option value="Not sure">Not sure</option>
            </select>
          </div>
          <div class="field">
            <label for="area">Area</label>
            <input id="area" name="Area" type="text" placeholder="Your locality" />
          </div>
          <div class="field full">
            <label for="message">Message</label>
            <textarea id="message" name="Message" required placeholder="Items, sizes, questions..."></textarea>
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
