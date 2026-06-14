@extends('layouts.app')
@section('content')

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="auth-logo-img">
        </div>

        <h1>Masuk</h1>

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
