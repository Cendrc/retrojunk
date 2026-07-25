@extends('admin.layout', ['title' => 'Detail Pesanan'])
@section('content')

<div class="admin-back">
    <a href="{{ route('admin.orders.index') }}">← Kembali ke Daftar Pesanan</a>
</div>

<div class="admin-detail-grid">
    <div class="admin-detail-main">
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>{{ $order->tracking_code }}</h2>
                <div class="admin-badge-group">
                    <span class="admin-badge admin-badge--{{ $order->order_status }}">
                        {{ $order->order_status_label }}
                    </span>
                    <span class="admin-badge admin-badge-payment--{{ $order->payment_status }}">
                        {{ $order->payment_status_label }}
                    </span>
                </div>
            </div>
            <div class="admin-panel__body">
                <div class="admin-info-grid">
                    <div>
                        <p class="admin-info-label">Tanggal Pesanan</p>
                        <p class="admin-info-value">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="admin-info-label">Metode Pembayaran</p>
                        <p class="admin-info-value">{{ $order->payment_method_label }}</p>
                    </div>
                    @if($order->confirmed_at)
                    <div>
                        <p class="admin-info-label">Dikonfirmasi</p>
                        <p class="admin-info-value">{{ $order->confirmed_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                    @if($order->shipped_at)
                    <div>
                        <p class="admin-info-label">Dikirim</p>
                        <p class="admin-info-value">{{ $order->shipped_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                    @if($order->delivered_at)
                    <div>
                        <p class="admin-info-label">Sampai</p>
                        <p class="admin-info-value">{{ $order->delivered_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>Informasi Pengiriman</h2>
            </div>
            <div class="admin-panel__body">
                <div class="admin-info-grid">
                    <div>
                        <p class="admin-info-label">Nama</p>
                        <p class="admin-info-value">{{ $order->name }}</p>
                    </div>
                    <div>
                        <p class="admin-info-label">Email</p>
                        <p class="admin-info-value">{{ $order->email }}</p>
                    </div>
                    <div>
                        <p class="admin-info-label">Telepon</p>
                        <p class="admin-info-value">{{ $order->phone }}</p>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <p class="admin-info-label">Alamat Lengkap</p>
                    <p class="admin-info-value">
                        {{ $order->address }}<br>
                        {{ $order->district }}, {{ $order->city }}<br>
                        {{ $order->province }} {{ $order->postal_code }}
                    </p>
                </div>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>Detail Produk</h2>
            </div>
            <div class="admin-panel__body">
                @foreach($order->items as $item)
                <div class="admin-order-item">
                    <img src="{{ asset($item->product_image) }}" alt="{{ $item->product_name }}"
                        onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                    <div class="admin-order-item__info">
                        <p class="admin-order-item__name">{{ $item->product_name }}</p>
                        @if($item->product && $item->product->size)
                            <p class="admin-order-item__size">Size: {{ $item->product->size }}</p>
                        @endif
                    </div>
                    <div class="admin-order-item__price">
                        IDR {{ number_format($item->product_price, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach

                <div class="admin-order-total">
                    <div class="admin-order-total__row">
                        <span>Subtotal</span>
                        <span>IDR {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="admin-order-total__row">
                        <span>Ongkir ({{ $order->shipping_zone }})</span>
                        <span>{{ $order->shipping_cost == 0 ? 'GRATIS' : 'IDR ' . number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="admin-order-total__row admin-order-total__row--grand">
                        <span>Total</span>
                        <span>IDR {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($order->payment_proof)
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>Bukti Pembayaran</h2>
            </div>
            <div class="admin-panel__body">
                <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran" 
                     style="max-width: 400px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
        </div>
        @endif
    </div>

    <div class="admin-detail-sidebar">
        <div class="admin-panel">
            <div class="admin-panel__header">
                <h2>Update Status</h2>
            </div>
            <div class="admin-panel__body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Order Status --}}
                    <div class="admin-form-group">
                        <label>Status Pesanan</label>
                        <select name="order_status" class="admin-input" required>
                            <option value="pending" @selected($order->order_status === 'pending')>Pending</option>
                            <option value="confirmed" @selected($order->order_status === 'confirmed')>Confirmed</option>
                            <option value="shipped" @selected($order->order_status === 'shipped')>Shipped</option>
                            <option value="delivered" @selected($order->order_status === 'delivered')>Delivered</option>
                            <option value="cancelled" @selected($order->order_status === 'cancelled')>Cancelled</option>
                        </select>
                    </div>

                    {{-- Payment Status --}}
                    <div class="admin-form-group">
                        <label>Status Pembayaran</label>
                        <select name="payment_status" class="admin-input" required>
                            <option value="unpaid" @selected($order->payment_status === 'unpaid')>Belum Dibayar</option>
                            <option value="awaiting_verification" @selected($order->payment_status === 'awaiting_verification')>Menunggu Verifikasi</option>
                            <option value="paid" @selected($order->payment_status === 'paid')>Sudah Dibayar</option>
                            <option value="rejected" @selected($order->payment_status === 'rejected')>Ditolak</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label>Kurir Pengiriman</label>
                        <select name="courier" class="admin-input">
                            <option value="">- Pilih Kurir -</option>
                            <option value="jne" @selected($order->courier === 'jne')>JNE</option>
                            <option value="jnt" @selected($order->courier === 'jnt')>J&T Express</option>
                            <option value="sicepat" @selected($order->courier === 'sicepat')>SiCepat</option>
                            <option value="anteraja" @selected($order->courier === 'anteraja')>AnterAja</option>
                            <option value="pos" @selected($order->courier === 'pos')>Pos Indonesia</option>
                            <option value="tiki" @selected($order->courier === 'tiki')>TIKI</option>
                            <option value="custom" @selected($order->courier === 'custom')>Lainnya</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label>Nomor Resi</label>
                        <input type="text" name="tracking_number" class="admin-input" 
                               value="{{ $order->tracking_number }}" placeholder="Contoh: JNE123456789">
                    </div>

                    <div class="admin-form-group">
                        <label>Catatan (Internal)</label>
                        <textarea name="notes" class="admin-input" rows="3" 
                                  placeholder="Catatan internal...">{{ $order->notes }}</textarea>
                    </div>

                    <button type="submit" class="admin-btn admin-btn--primary admin-btn--full">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <div class="admin-panel">
            <div class="admin-panel__body">
                <a href="{{ route('tracking.show', $order->tracking_code) }}" 
                   target="_blank" class="admin-btn admin-btn--secondary admin-btn--full">
                    Lihat Halaman Tracking Customer →
                </a>
            </div>
        </div>
    </div>
</div>

@endsection