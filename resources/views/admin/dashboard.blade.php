@extends('admin.layout', ['title' => 'Dashboard'])
@section('content')

{{-- Stats Cards (Clickable) --}}
<div class="admin-stats">
    <a href="{{ route('admin.orders.index') }}" class="admin-stat-card">
        <div class="admin-stat-card__icon admin-stat-card__icon--blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
            </svg>
        </div>
        <div>
            <p class="admin-stat-card__label">Total Pesanan</p>
            <h3 class="admin-stat-card__value">{{ $totalOrders }}</h3>
        </div>
    </a>

    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="admin-stat-card">
        <div class="admin-stat-card__icon admin-stat-card__icon--orange">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <p class="admin-stat-card__label">Pending</p>
            <h3 class="admin-stat-card__value">{{ $pendingOrders }}</h3>
        </div>
    </a>

    <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="admin-stat-card">
        <div class="admin-stat-card__icon admin-stat-card__icon--purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="1" y="3" width="15" height="13"/>
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                <circle cx="5.5" cy="18.5" r="2.5"/>
                <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
        </div>
        <div>
            <p class="admin-stat-card__label">Dalam Pengiriman</p>
            <h3 class="admin-stat-card__value">{{ $shippedOrders }}</h3>
        </div>
    </a>

    <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="admin-stat-card">
        <div class="admin-stat-card__icon admin-stat-card__icon--green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="1" x2="12" y2="23"/>
                <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div>
            <p class="admin-stat-card__label">Total Revenue</p>
            <h3 class="admin-stat-card__value">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
    </a>

    <a href="{{ route('admin.products.index') }}" class="admin-stat-card">
        <div class="admin-stat-card__icon admin-stat-card__icon--olive">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
            </svg>
        </div>
        <div>
            <p class="admin-stat-card__label">Total Produk</p>
            <h3 class="admin-stat-card__value">{{ $totalProducts }}</h3>
        </div>
    </a>

    <a href="{{ route('admin.customers.index') }}" class="admin-stat-card">
        <div class="admin-stat-card__icon admin-stat-card__icon--brown">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
        </div>
        <div>
            <p class="admin-stat-card__label">Total Customer</p>
            <h3 class="admin-stat-card__value">{{ $totalCustomers }}</h3>
        </div>
    </a>
</div>

{{-- Recent Orders & Best Selling --}}
<div class="admin-grid">
    <div class="admin-panel">
        <div class="admin-panel__header">
            <h2>Pesanan Terbaru</h2>
            <a href="{{ route('admin.orders.index') }}" class="admin-link">Lihat Semua →</a>
        </div>
        <div class="admin-panel__body">
            @if($recentOrders->count() > 0)
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order->id) }}" class="admin-table__link">{{ $order->tracking_code }}</a></td>
                        <td>{{ $order->name }}</td>
                        <td>IDR {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="admin-badge admin-badge--{{ $order->status }}">{{ $order->status_label }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="admin-empty">Belum ada pesanan.</p>
            @endif
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel__header">
            <h2>Produk Terlaris</h2>
        </div>
        <div class="admin-panel__body">
            @if($bestSelling->count() > 0)
            @foreach($bestSelling as $item)
            <div class="best-selling-item">
                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                <div class="best-selling-item__info">
                    <p class="best-selling-item__name">{{ $item['name'] }}</p>
                    <p class="best-selling-item__count">{{ $item['count'] }} terjual</p>
                </div>
            </div>
            @endforeach
            @else
            <p class="admin-empty">Belum ada data penjualan.</p>
            @endif
        </div>
    </div>
</div>

@endsection