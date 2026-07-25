@extends('layouts.app')
@section('content')

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="auth-logo-img">
        </div>

        <h1>Masuk</h1>

        {{-- Info Alert: Kalau user dialihkan dari checkout --}}
        @if(session()->has('url.intended') && str_contains(session('url.intended'), 'checkout'))
        <div class="alert-info-login">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <div>
                <strong>Silakan login terlebih dahulu</strong>
                <p>Kamu perlu masuk ke akun untuk melanjutkan pemesanan</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="Email"
                    value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-input" placeholder="Password" required>
            </div>
            <div class="form-check form-check--row">
                <label><input type="checkbox" name="remember"> Ingat saya</label>
                <a href="{{ route('password.forgot') }}" class="forgot-link">Lupa password?</a>
            </div>
            <button type="submit" class="btn-payment">Masuk</button>
        </form>

        <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </div>
</section>
@endsection
