@extends('admin.layout', ['title' => 'Pesanan'])
@section('content')

<div class="admin-toolbar">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="admin-filters">
        <input type="text" name="search" class="admin-input" 
               placeholder="Cari kode pesanan, email, atau nama..." 
               value="{{ request('search') }}">
        
        {{-- Filter Order Status --}}
        <select name="order_status" class="admin-input">
            <option value="">Semua Status Pesanan</option>
            <option value="pending" @selected(request('order_status') === 'pending')>Pending</option>
            <option value="confirmed" @selected(request('order_status') === 'confirmed')>Confirmed</option>
            <option value="shipped" @selected(request('order_status') === 'shipped')>Shipped</option>
            <option value="delivered" @selected(request('order_status') === 'delivered')>Delivered</option>
            <option value="cancelled" @selected(request('order_status') === 'cancelled')>Cancelled</option>
        </select>

        {{-- Filter Payment Status --}}
        <select name="payment_status" class="admin-input">
            <option value="">Semua Status Pembayaran</option>
            <option value="unpaid" @selected(request('payment_status') === 'unpaid')>Belum Dibayar</option>
            <option value="awaiting_verification" @selected(request('payment_status') === 'awaiting_verification')>Menunggu Verifikasi</option>
            <option value="paid" @selected(request('payment_status') === 'paid')>Sudah Dibayar</option>
            <option value="rejected" @selected(request('payment_status') === 'rejected')>Ditolak</option>
        </select>

        <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
        
        @if(request('search') || request('order_status') || request('payment_status'))
        <a href="{{ route('admin.orders.index') }}" class="admin-btn admin-btn--secondary">Reset</a>
        @endif
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        @if($orders->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Aksi</th>
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status Pesanan</th>
                    <th>Status Bayar</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="admin-btn admin-btn--small">Detail</a>
                    </td>
                    <td><strong>{{ $order->tracking_code }}</strong></td>
                    <td>
                        <p>{{ $order->name }}</p>
                        <small>{{ $order->email }}</small>
                    </td>
                    <td>IDR {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>{{ $order->payment_method_label }}</td>
                    <td>
                        <span class="admin-badge admin-badge--{{ $order->order_status }}">
                            {{ $order->order_status_label }}
                        </span>
                    </td>
                    <td>
                        <span class="admin-badge admin-badge-payment--{{ $order->payment_status }}">
                            {{ $order->payment_status_label }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="admin-pagination">
            {{ $orders->links() }}
        </div>
        @else
        <p class="admin-empty">Tidak ada pesanan ditemukan.</p>
        @endif
    </div>
</div>

@endsection