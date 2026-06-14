@extends('layouts.app')
@section('content')

<section class="orders-page">
    <div class="orders-page__inner">
        <div class="orders-header">
            <a href="{{ route('account') }}" class="orders-back">← Kembali</a>
            <h1>Riwayat Pesanan</h1>
            <p>{{ $orders->count() }} pesanan ditemukan</p>
        </div>

        @if($orders->count() > 0)
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card">
                <div class="order-card__header">
                    <div>
                        <p class="order-card__id">{{ $order->tracking_code ?? 'Order #' . $order->id }}</p>
                        <p class="order-card__date">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <span class="order-card__status order-card__status--{{ $order->status }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                <div class="order-card__items">
                    @foreach($order->items as $item)
                    <div class="order-card__item">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                             onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                        <div>
                            <p class="order-item__name">{{ $item['name'] }}</p>
                            <p class="order-item__price">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="order-card__footer">
                    <div>
                        <p class="order-card__label">Total Pembayaran</p>
                        <p class="order-card__total">IDR {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                    <div class="order-card__shipping">
                        <p class="order-card__label">Dikirim ke:</p>
                        <p>{{ $order->name }}, {{ $order->city }}</p>
                    </div>
                </div>

                <div class="order-card__action">
                    @if($order->tracking_code)
                    <a href="{{ route('tracking.show', $order->tracking_code) }}" class="btn-track">
                        Lacak Pesanan →
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state__icon">📦</div>
            <h2>Belum ada pesanan</h2>
            <p>Mulai belanja dan pesananmu akan muncul di sini.</p>
            <a href="{{ route('home') }}" class="btn-primary">Mulai Belanja</a>
        </div>
        @endif
    </div>
</section>

@endsection