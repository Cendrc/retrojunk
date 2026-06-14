@extends('layouts.app')
@section('content')

<section class="coming-soon">
    <div class="coming-soon__inner">
        <div class="coming-soon__icon">
            <svg viewBox="0 0 100 100" fill="none">
                <circle cx="50" cy="50" r="45" stroke="white" stroke-width="2" opacity="0.4"/>
                <path d="M50 30v22l14 8" stroke="white" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
        <p class="coming-soon__label">{{ $title }}</p>
        <h1>Coming Soon</h1>
        <p class="coming-soon__desc">
            Koleksi {{ $title }} sedang dalam proses kurasi.<br>
            Tunggu update terbaru kami!
        </p>
        <div class="coming-soon__actions">
            <a href="{{ route('home') }}" class="btn-primary">Kembali ke Beranda</a>
            <a href="{{ route('new-arrivals') }}" class="btn-outline">Lihat Koleksi Lain</a>
        </div>
    </div>
</section>

@endsection