@extends('layouts.shop')

@section('dress_page', 'contact')
@section('title', 'Contact — Richierich')
@section('meta_description', 'Contact Richierich. Enquire on WhatsApp.')

@push('head')
  @include('partials.playfair')
@endpush

@section('content')
@php
  $wa = \App\Models\Store::siteWhatsapp();
  $waPretty = $wa;
  if (strlen($wa) > 10) {
      $waPretty = '+'.substr($wa, 0, strlen($wa) - 10).' '.substr($wa, -10);
  } elseif ($wa) {
      $waPretty = '+'.$wa;
  }
  $interests = collect($catalog['categories'] ?? [])->pluck('name')->filter()->values();
  if ($interests->isEmpty()) {
      $interests = collect(['Saree', 'Lehengas', 'Western', 'Kids', 'Kurtis & Sets']);
  }
@endphp
<section class="shop-hero contact-hero">
  <div class="container">
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a><span>›</span><span>Contact</span>
    </div>
    <h1>Contact Richierich</h1>
  </div>
</section>

<section class="section contact-section">
  <div class="container contact-grid">
    <div class="contact-card">
      <h2>Get in touch</h2>
      <p>Have a question or need help with sizes, fabrics or availability? We’re here to help.</p>
      @if($wa)
        <div class="contact-wa">
          <span class="contact-wa-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path fill="currentColor" d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.5 0 .2 5.3.2 11.82c0 2.08.55 4.11 1.6 5.9L0 24l6.45-1.69a11.8 11.8 0 005.58 1.42h.01c6.54 0 11.84-5.3 11.84-11.82 0-3.16-1.23-6.13-3.36-8.43zM12.05 21.5h-.01a9.7 9.7 0 01-4.94-1.35l-.35-.21-3.82 1 1.02-3.72-.23-.38a9.7 9.7 0 01-1.5-5.18c0-5.36 4.36-9.72 9.73-9.72a9.66 9.66 0 016.88 2.85 9.66 9.66 0 012.85 6.88c0 5.36-4.37 9.73-9.73 9.73zm5.33-7.28c-.29-.15-1.72-.85-1.99-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.14-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.43-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.43-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.57-.48-.49-.66-.5h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.11-.26-.18-.55-.33z"/></svg>
          </span>
          <div>
            <span class="contact-wa-label">WhatsApp</span>
            <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">{{ $waPretty }}</a>
          </div>
        </div>
        <a class="btn btn-primary contact-btn" href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener noreferrer">
          <svg class="ico-wa" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.5 0 .2 5.3.2 11.82c0 2.08.55 4.11 1.6 5.9L0 24l6.45-1.69a11.8 11.8 0 005.58 1.42h.01c6.54 0 11.84-5.3 11.84-11.82 0-3.16-1.23-6.13-3.36-8.43zM12.05 21.5h-.01a9.7 9.7 0 01-4.94-1.35l-.35-.21-3.82 1 1.02-3.72-.23-.38a9.7 9.7 0 01-1.5-5.18c0-5.36 4.36-9.72 9.73-9.72a9.66 9.66 0 016.88 2.85 9.66 9.66 0 012.85 6.88c0 5.36-4.37 9.73-9.73 9.73zm5.33-7.28c-.29-.15-1.72-.85-1.99-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.14-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.43-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.43-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.57-.48-.49-.66-.5h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.11-.26-.18-.55-.33z"/></svg>
          Chat on WhatsApp
          <span aria-hidden="true">→</span>
        </a>
      @endif

      <div class="contact-shop">
        <h2>Shop dresses</h2>
        <p>Explore our latest sarees, lehengas, western dresses and more.</p>
        <a class="btn btn-outline contact-btn" href="{{ route('shop.products') }}">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M6 6h15l-1.5 9h-12L6 6Zm0 0L5 3H2"/><circle cx="9" cy="20" r="1.4" fill="currentColor" stroke="none"/><circle cx="18" cy="20" r="1.4" fill="currentColor" stroke="none"/></svg>
          View collection
          <span aria-hidden="true">→</span>
        </a>
      </div>
    </div>

    <div class="contact-card">
      <h2>Quick enquiry</h2>
      <p>Submit the form or open WhatsApp with your details.</p>
      <form data-wa-form="Hi Richierich, enquiry from website:">
        <div class="form-grid">
          <div class="field">
            <label for="name">Name</label>
            <input id="name" name="Name" type="text" required placeholder="Your name" />
          </div>
          <div class="field">
            <label for="phone">WhatsApp number</label>
            <input id="phone" name="Phone" type="tel" required placeholder="10-digit mobile number" />
          </div>
          <div class="field">
            <label for="interest">Interested in</label>
            <select id="interest" name="Interest">
              @foreach($interests as $name)
                <option value="{{ $name }}">{{ $name }}</option>
              @endforeach
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
        <button class="btn btn-primary contact-btn contact-submit" type="submit">
          <svg class="ico-wa" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.5 0 .2 5.3.2 11.82c0 2.08.55 4.11 1.6 5.9L0 24l6.45-1.69a11.8 11.8 0 005.58 1.42h.01c6.54 0 11.84-5.3 11.84-11.82 0-3.16-1.23-6.13-3.36-8.43zM12.05 21.5h-.01a9.7 9.7 0 01-4.94-1.35l-.35-.21-3.82 1 1.02-3.72-.23-.38a9.7 9.7 0 01-1.5-5.18c0-5.36 4.36-9.72 9.73-9.72a9.66 9.66 0 016.88 2.85 9.66 9.66 0 012.85 6.88c0 5.36-4.37 9.73-9.73 9.73zm5.33-7.28c-.29-.15-1.72-.85-1.99-.95-.27-.1-.46-.15-.66.15-.2.29-.76.95-.93 1.14-.17.2-.34.22-.63.07-.29-.15-1.22-.45-2.33-1.43-.86-.77-1.44-1.72-1.61-2.01-.17-.29-.02-.45.13-.6.13-.13.29-.34.43-.51.15-.17.2-.29.29-.49.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.57-.48-.49-.66-.5h-.56c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.11-.26-.18-.55-.33z"/></svg>
          Send on WhatsApp
          <span aria-hidden="true">→</span>
        </button>
      </form>
    </div>
  </div>
</section>
@endsection
