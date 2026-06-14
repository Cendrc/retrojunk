@extends('layouts.app')
@section('content')

<section class="success-page">
    <div class="success-page__inner">
        <div class="success-icon">
            <svg viewBox="0 0 60 60" fill="none">
                <circle cx="30" cy="30" r="28" stroke="#5c6b3a" stroke-width="2"/>
                <path d="M18 30l9 9 15-16" stroke="#5c6b3a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Pesanan Berhasil!</h1>
        <p>Terima kasih telah berbelanja di Retro Junk.<br>
           Kami akan segera memproses pesananmu.</p>

        @if($order)
        <div class="tracking-code-card">
            <p class="tracking-code-card__label">Kode Pelacakan Pesanan</p>
            <h2 class="tracking-code-card__code">{{ $order->tracking_code }}</h2>
            <p class="tracking-code-card__hint">
                Simpan kode ini untuk melacak pesananmu di halaman <a href="{{ route('tracking.index') }}">Lacak Pesanan</a>
            </p>
        </div>
        @endif

        <div class="success-actions">
            @if($order)
            <a href="{{ route('tracking.show', $order->tracking_code) }}" class="btn-primary">Lacak Pesanan</a>
            @endif
            <a href="{{ route('home') }}" class="btn-outline">Kembali Belanja</a>
            @auth
            <a href="{{ route('orders') }}" class="btn-outline">Lihat Semua Pesanan</a>
            @endauth
        </div>
    </div>
</section>

<style>
.tracking-code-card {
    background: rgba(255,255,255,0.1);
    border: 2px dashed rgba(255,255,255,0.3);
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem auto;
    max-width: 400px;
}

.tracking-code-card__label {
    color: rgba(255,255,255,0.7) !important;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 0.5rem;
}

.tracking-code-card__code {
    font-family: monospace;
    font-size: 1.8rem;
    color: white;
    font-weight: 700;
    letter-spacing: 0.05em;
    margin-bottom: 0.75rem;
}

.tracking-code-card__hint {
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
}

.tracking-code-card__hint a {
    color: white;
    text-decoration: underline;
}
</style>

@endsection