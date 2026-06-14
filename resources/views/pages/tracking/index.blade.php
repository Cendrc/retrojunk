@extends('layouts.app')
@section('content')

<section class="tracking-page">
    <div class="tracking-page__inner">
        <div class="tracking-page__header">
            <div class="tracking-page__icon">
                <svg viewBox="0 0 80 80" fill="none">
                    <rect x="10" y="20" width="60" height="40" rx="4" stroke="white" stroke-width="2"/>
                    <path d="M10 30h60" stroke="white" stroke-width="2"/>
                    <circle cx="22" cy="45" r="5" stroke="white" stroke-width="2"/>
                    <circle cx="58" cy="45" r="5" stroke="white" stroke-width="2"/>
                    <path d="M27 45h26" stroke="white" stroke-width="2"/>
                </svg>
            </div>
            <h1>Lacak Pesanan</h1>
            <p>Masukkan kode pesanan & email untuk melihat status pengiriman</p>
        </div>

        <div class="tracking-form-card">
            @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('tracking.search') }}" method="POST" class="tracking-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">Kode Pesanan</label>
                    <input type="text" name="tracking_code" class="form-input" 
                           placeholder="Contoh: RJ-2026-ABC123" 
                           value="{{ old('tracking_code') }}" required autofocus>
                    <small class="form-hint">Kode dikirim ke email saat pesanan dibuat</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" 
                           placeholder="Email yang digunakan saat checkout" 
                           value="{{ old('email') }}" required>
                </div>

                <button type="submit" class="btn-payment">Lacak Pesanan</button>
            </form>

            @auth
            <div class="tracking-shortcut">
                <p>Atau lihat semua pesanan kamu:</p>
                <a href="{{ route('orders') }}" class="btn-outline-dark">Riwayat Pesanan Saya →</a>
            </div>
            @endauth
        </div>
    </div>
</section>

@endsection