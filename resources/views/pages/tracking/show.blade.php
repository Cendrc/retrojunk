@extends('layouts.app')
@section('content')

<section class="tracking-detail">
    <div class="tracking-detail__inner">
        {{-- Header --}}
        <div class="tracking-detail__header">
            <a href="{{ route('tracking.index') }}" class="tracking-back">← Cari Pesanan Lain</a>
            <div class="tracking-code-display">
                <p class="tracking-code-display__label">Kode Pesanan</p>
                <h1 class="tracking-code-display__code">{{ $order->tracking_code }}</h1>
            </div>
        </div>

        {{-- Status Badges (Order + Payment) --}}
        <div class="tracking-status-group">
            <div class="tracking-status-badge tracking-status-badge--{{ $order->order_status }}">
                {{ $order->order_status_label }}
            </div>
            @if($order->order_status !== 'cancelled')
            <div class="tracking-status-badge tracking-status-badge-payment--{{ $order->payment_status }}">
                {{ $order->payment_status_label }}
            </div>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="tracking-card">
            <h2>Timeline Pengiriman</h2>
            <div class="tracking-timeline">
                @foreach($order->tracking_steps as $index => $step)
                <div class="timeline-step {{ $step['completed'] ? 'completed' : '' }} {{ ($step['cancelled'] ?? false) ? 'cancelled' : '' }} {{ $loop->last ? 'last' : '' }}">
                    <div class="timeline-step__icon">
                        @if($step['icon'] === 'package')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 01-8 0"/>
                            </svg>
                        @elseif($step['icon'] === 'cancel')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                        @elseif($step['icon'] === 'check')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        @elseif($step['icon'] === 'truck')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        @elseif($step['icon'] === 'home')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        @endif
                    </div>
                    <div class="timeline-step__content">
                        <p class="timeline-step__label">{{ $step['label'] }}</p>
                        <p class="timeline-step__desc">{{ $step['description'] }}</p>
                        @if($step['date'])
                        <p class="timeline-step__date" data-local-time="{{ \Carbon\Carbon::parse($step['date'])->toIso8601String() }}">{{ \Carbon\Carbon::parse($step['date'])->translatedFormat('d M Y, H:i') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tracking Number (if shipped) --}}
        @if($order->tracking_number && in_array($order->order_status, ['shipped', 'delivered']))
        <div class="tracking-card tracking-card--resi">
            <h2>Informasi Kurir</h2>
            <div class="resi-info">
                <div class="resi-info__row">
                    <span class="resi-info__label">Kurir:</span>
                    <span class="resi-info__value">{{ $order->courier_label }}</span>
                </div>
                <div class="resi-info__row">
                    <span class="resi-info__label">Nomor Resi:</span>
                    <span class="resi-info__value resi-info__value--code">{{ $order->tracking_number }}</span>
                    <button class="resi-copy-btn" data-copy="{{ $order->tracking_number }}">Salin</button>
                </div>
            </div>
            @if($order->courier_tracking_url)
            <a href="{{ $order->courier_tracking_url }}" target="_blank" class="btn-track-courier">
                Lacak di {{ $order->courier_label }} →
            </a>
            @endif
        </div>
        @endif

        {{-- Detail Pesanan --}}
        <div class="tracking-card">
            <h2>Detail Pesanan</h2>
            <div class="order-items">
                @foreach($order->items as $item)
                <div class="order-item">
                    <div class="order-item__img">
                        <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}"
                            onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    </div>
                    <div class="order-item__info">
                        <p class="order-item__name">{{ $item->product_name }}</p>
                        @if($item->product && $item->product->size)
                            <p class="order-item__size">Size: {{ $item->product->size }}</p>
                        @endif
                    </div>
                    <div class="order-item__price">
                        IDR {{ number_format($item->product_price, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>

            <div class="order-summary">
                <div class="order-summary__row">
                    <span>Subtotal</span>
                    <span>IDR {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="order-summary__row">
                    <span>Ongkir ({{ $order->shipping_zone }})</span>
                    <span>{{ $order->shipping_cost == 0 ? 'GRATIS' : 'IDR ' . number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="order-summary__row order-summary__row--grand">
                    <span>Total</span>
                    <span>IDR {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Alamat Pengiriman --}}
        <div class="tracking-card">
            <h2>Alamat Pengiriman</h2>
            <div class="shipping-address">
                <p><strong>{{ $order->name }}</strong></p>
                <p>📱 {{ $order->phone }}</p>
                <p>{{ $order->address }}</p>
                <p>{{ $order->district }}, {{ $order->city }}</p>
                <p>{{ $order->province }} {{ $order->postal_code }}</p>
            </div>
            <div class="payment-method-display">
                <span class="payment-method-display__label">Metode Pembayaran:</span>
                <span class="payment-method-display__value">{{ $order->payment_method_label }}</span>
            </div>
        </div>

        {{-- Action --}}
        <div class="tracking-actions">
            <a href="{{ route('home') }}" class="btn-primary">Lanjut Belanja</a>
            @auth
            <a href="{{ route('orders') }}" class="btn-outline">Lihat Semua Pesanan</a>
            @endauth
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.resi-copy-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            this.textContent = '✓ Tersalin';
            setTimeout(() => this.textContent = 'Salin', 2000);
        });
    });
});
</script>

@endsection