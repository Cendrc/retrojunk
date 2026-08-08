@extends('layouts.app')
@section('content')

<section class="orders-page">
    <div class="orders-page__inner">
        <div class="orders-header">
            <a href="{{ route('account') }}" class="orders-back">← Kembali</a>
            <h1>Riwayat Pesanan</h1>
            <p>{{ $orders->count() }} pesanan ditemukan</p>
        </div>

        @if(session('success'))
        <div class="alert-success" style="margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert-error" style="margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        @if($orders->count() > 0)
        <div class="orders-list">
            @foreach($orders as $order)
            <div class="order-card">
                <div class="order-card__header">
                    <div>
                        <p class="order-card__id">{{ $order->tracking_code ?? 'Order #' . $order->id }}</p>
                        <p class="order-card__date">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="order-card__status-group">
                        <span class="order-card__status order-card__status--{{ $order->order_status }}">
                            {{ $order->order_status_label }}
                        </span>
                        <span class="order-card__status order-card__status-payment--{{ $order->payment_status }}">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                </div>

                <div class="order-card__items">
                    @foreach($order->items as $item)
                    <div class="order-card__item">
                        <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}"
                            onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                        <div>
                            <p class="order-item__name">{{ $item->product_name }}</p>
                            <p class="order-item__price">IDR {{ number_format($item->product_price, 0, ',', '.') }}</p>
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

                    @if($order->order_status === 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST"
                          onsubmit="return confirm('Yakin mau membatalkan pesanan ini?');" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-cancel-order">Batalkan Pesanan</button>
                    </form>
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