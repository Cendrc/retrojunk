@extends('layouts.app')
@section('content')

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="auth-logo-img">
        </div>

        <h1>Reset Password</h1>
        <p class="auth-subtitle">Buat password baru untuk akun <strong>{{ $email }}</strong></p>

        @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <input type="password" name="password" class="form-input" 
                       placeholder="Password Baru (min. 8 karakter)" required autofocus>
            </div>

            <div class="form-group">
                <input type="password" name="password_confirmation" class="form-input" 
                       placeholder="Konfirmasi Password Baru" required>
            </div>

            <button type="submit" class="btn-payment">Simpan Password Baru</button>
        </form>

        <p class="auth-switch">
            <a href="{{ route('login') }}">← Kembali ke Login</a>
        </p>
    </div>
</section>

@endsection