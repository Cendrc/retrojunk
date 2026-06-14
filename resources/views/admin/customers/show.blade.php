@extends('admin.layout', ['title' => 'Detail Customer'])
@section('content')

<div class="admin-back">
    <a href="{{ route('admin.customers.index') }}">← Kembali ke Daftar Customer</a>
</div>

<div class="admin-detail-grid">
    <div class="admin-detail-main">
        <div class="admin-panel">
            <div class="admin-panel__body">
                <div class="customer-detail-header">
                    <div class="customer-avatar customer-avatar--large">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2>{{ $customer->name }}</h2>
                        <p style="color: #888;">{{ $customer->email }}</p>
                        <p style="color: #888; font-size: 0.85rem;">Member sejak {{ $customer->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>Riwayat Pesanan ({{ $orders->count() }})</h2>
            </div>
            <div class="admin-panel__body">
                @if($orders->count() > 0)
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><strong>{{ $order->tracking_code }}</strong></td>
                            <td>IDR {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><span class="admin-badge admin-badge--{{ $order->status }}">{{ $order->status_label }}</span></td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="admin-btn admin-btn--small">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="admin-empty">Customer belum memiliki pesanan.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="admin-detail-sidebar">
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>Statistik</h2>
            </div>
            <div class="admin-panel__body">
                <div class="admin-stat-row">
                    <span>Total Pesanan</span>
                    <strong>{{ $orders->count() }}</strong>
                </div>
                <div class="admin-stat-row">
                    <span>Selesai</span>
                    <strong>{{ $orders->where('status', 'delivered')->count() }}</strong>
                </div>
                <div class="admin-stat-row">
                    <span>Total Belanja</span>
                    <strong>IDR {{ number_format($orders->whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total'), 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection