@extends('layouts.app')
@section('content')

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="auth-logo-img">
        </div>

        <h1>Daftar</h1>

        @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" name="name" class="form-input" placeholder="Nama Lengkap"
                       value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="Email"
                       value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-input" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="password" name="password_confirmation" class="form-input"
                       placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit" class="btn-payment">Daftar</button>
        </form>

        <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </div>
</section>
@endsection
