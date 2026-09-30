@extends('layouts.admin')

@section('title', 'Login')

@section('content')
<div class="login-wrap">
  <div class="card login-card">
    <div class="login-brand">
      <img src="{{ asset('assets/dress_logo.png') }}" alt="Richie Rich" />
      <div>
        <strong>Richie Rich</strong>
        <div class="muted" style="font-size:0.8rem;">Admin access</div>
      </div>
    </div>
    <h1>Sign in</h1>
    <p class="muted" style="margin-top:0;">Manage products, categories, banners and settings.</p>
    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('admin.login.submit') }}">
      @csrf
      <div class="field" style="margin-bottom:1rem;">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
      </div>
      <div class="field" style="margin-bottom:1.25rem;">
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" />
      </div>
      <button type="submit" class="btn btn-primary btn-block">Sign in</button>
    </form>
  </div>
</div>
@endsection
