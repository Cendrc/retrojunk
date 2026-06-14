@extends('admin.layout', ['title' => 'Pesanan'])
@section('content')

<div class="admin-toolbar">
    <form action="{{ route('admin.orders.index') }}" method="GET" class="admin-filters">
        <input type="text" name="search" class="admin-input" 
               placeholder="Cari kode pesanan, email, atau nama..." 
               value="{{ request('search') }}">
        
        <select name="status" class="admin-input">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
            <option value="shipped" @selected(request('status') === 'shipped')>Shipped</option>
            <option value="delivered" @selected(request('status') === 'delivered')>Delivered</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
        </select>

        <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
        
        @if(request('search') || request('status'))
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
                    <th>Kode</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><strong>{{ $order->tracking_code }}</strong></td>
                    <td>
                        <p>{{ $order->name }}</p>
                        <small>{{ $order->email }}</small>
                    </td>
                    <td>IDR {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>{{ $order->payment_method_label }}</td>
                    <td><span class="admin-badge admin-badge--{{ $order->status }}">{{ $order->status_label }}</span></td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="admin-btn admin-btn--small">Detail</a>
                    </td>
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