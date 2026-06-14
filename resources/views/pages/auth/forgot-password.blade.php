@extends('layouts.app')
@section('content')

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="auth-logo-img">
        </div>

        <h1>Lupa Password?</h1>
        <p class="auth-subtitle">Masukkan email yang terdaftar untuk reset password.</p>

        @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.check') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="Email"
                       value="{{ old('email') }}" required autofocus>
            </div>

            <button type="submit" class="btn-payment">Lanjutkan</button>
        </form>

        <p class="auth-switch">
            Ingat password kamu? <a href="{{ route('login') }}">Kembali ke Login</a>
        </p>
    </div>
</section>

@endsection