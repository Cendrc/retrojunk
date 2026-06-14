@extends('layouts.app')
@section('content')

<section class="account-page">
    <div class="account-page__inner">
        {{-- Header --}}
        <div class="account-header">
            <div class="account-header__avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="account-header__info">
                <p class="account-header__welcome">Halo,</p>
                <h1>{{ auth()->user()->name }}</h1>
                <p class="account-header__email">{{ auth()->user()->email }}</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="account-stats">
            <div class="stat-card">
                <p class="stat-card__label">Member Sejak</p>
                <p class="stat-card__value">{{ auth()->user()->created_at->format('M Y') }}</p>
            </div>
            <div class="stat-card">
                <p class="stat-card__label">Total Pesanan</p>
                <p class="stat-card__value">{{ \App\Models\Order::where('email', auth()->user()->email)->count() }}</p>
            </div>
            <div class="stat-card">
                <p class="stat-card__label">Status</p>
                <p class="stat-card__value">Aktif</p>
            </div>
        </div>

        {{-- Menu --}}
        <div class="account-menu">
            <a href="{{ route('orders') }}" class="account-menu__item">
                <div class="account-menu__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                </div>
                <div class="account-menu__text">
                    <p class="account-menu__title">Riwayat Pesanan</p>
                    <p class="account-menu__desc">Lihat semua pesanan kamu</p>
                </div>
                <div class="account-menu__arrow">→</div>
            </a>

            <a href="{{ route('home') }}" class="account-menu__item">
                <div class="account-menu__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <div class="account-menu__text">
                    <p class="account-menu__title">Lanjut Belanja</p>
                    <p class="account-menu__desc">Jelajahi koleksi terbaru kami</p>
                </div>
                <div class="account-menu__arrow">→</div>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="account-menu__form">
                @csrf
                <button type="submit" class="account-menu__item account-menu__item--logout">
                    <div class="account-menu__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </div>
                    <div class="account-menu__text">
                        <p class="account-menu__title">Keluar</p>
                        <p class="account-menu__desc">Logout dari akun ini</p>
                    </div>
                    <div class="account-menu__arrow">→</div>
                </button>
            </form>
        </div>
    </div>
</section>

@endsection