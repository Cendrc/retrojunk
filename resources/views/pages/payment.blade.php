@extends('layouts.app')
@section('content')

<div class="checkout-page__logo-wrap">
    <a href="{{ route('home') }}" class="checkout-page__logo">
        <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="checkout-page__logo-img">
    </a>
</div>

<section class="payment-page">
    <div class="payment-page__inner">
        <div class="payment-header">
            <h1>{{ $order->payment_method === 'cod' ? 'Pesanan Diterima' : 'Selesaikan Pembayaran' }}</h1>
            <p>Order ID: <strong>#{{ $order->id }}</strong></p>
        </div>

       {{-- QRIS --}}
        @if($order->payment_method === 'qris')
        <div class="payment-card">
            <h2>Scan QRIS untuk Bayar</h2>
            <div class="qris-box">
                <img src="{{ asset('images/qris-code.png') }}" alt="QRIS Code" class="qris-image">
                <div class="qris-info">
                    <p class="qris-merchant">Retro Junk Preloved</p>
                </div>
            </div>
            <div class="payment-amount">
                <p>Total yang harus dibayar:</p>
                <h3>IDR {{ number_format($order->total, 0, ',', '.') }}</h3>
            </div>
            <p class="payment-help">Gunakan aplikasi GoPay, OVO, Dana, ShopeePay, atau e-wallet/m-banking yang mendukung QRIS.</p>
        </div>
        @endif

        {{-- Bank Transfer --}}
        @if($order->payment_method === 'bank_transfer')
        <div class="payment-card">
            <h2>Transfer ke Virtual Account Berikut</h2>

            <div class="bank-list">
                <div class="bank-item">
                    <div class="bank-item__logo">{{ strtoupper($order->va_bank) }}</div>
                    <div class="bank-item__info">
                        <p class="bank-item__name">Virtual Account {{ strtoupper($order->va_bank) }}</p>
                        <p class="bank-item__number">{{ $order->va_number }}</p>
                        <p class="bank-item__owner">Nomor VA khusus untuk pesanan ini</p>
                    </div>
                    <button class="bank-item__copy" data-copy="{{ $order->va_number }}">Salin</button>
                </div>
            </div>

            <div class="payment-amount">
                <div class="payment-breakdown">
                    <div class="payment-breakdown__row">
                        <span>Subtotal Produk:</span>
                        <span>IDR {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="payment-breakdown__row">
                        <span>Ongkir ({{ $order->shipping_zone }}):</span>
                        <span>{{ $order->shipping_cost == 0 ? 'GRATIS' : 'IDR ' . number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p>Total yang harus dibayar:</p>
                <h3>IDR {{ number_format($order->total, 0, ',', '.') }}</h3>
            </div>
            <p class="payment-help">Transfer sesuai nominal di atas melalui ATM, m-banking, atau internet banking {{ strtoupper($order->va_bank) }}. Status pembayaran akan diperbarui otomatis begitu pembayaran diterima.</p>

        {{-- COD --}}
        @if($order->payment_method === 'cod')
        <div class="payment-card payment-card--cod">
            <div class="cod-icon">
                <svg viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="45" stroke="#5c6b3a" stroke-width="2"/>
                    <path d="M30 50l15 15 25-30" stroke="#5c6b3a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2>Pesanan COD Berhasil Dibuat!</h2>
            <p class="cod-desc">Pesanan kamu akan segera kami proses dan dikirim ke alamat:</p>
            <div class="cod-address">
                <p><strong>{{ $order->name }}</strong></p>
                <p>{{ $order->address }}</p>
                <p>{{ $order->district }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
                <p>📱 {{ $order->phone }}</p>
            </div>
            <div class="cod-amount">
                <p>Total yang harus dibayar saat barang sampai:</p>
                <h3>IDR {{ number_format($order->total, 0, ',', '.') }}</h3>
            </div>
            <p class="payment-help">💡 Siapkan uang pas saat kurir tiba untuk mempercepat proses.</p>
        </div>
        @endif

        {{-- Upload Bukti — WAJIB untuk QRIS (manual, tanpa auto-verifikasi) --}}
        @if($order->payment_method === 'qris')
        <div class="payment-card">
            <h2>Upload Bukti Pembayaran</h2>
            <p>Setelah transfer, upload bukti pembayaran untuk konfirmasi oleh admin.</p>

            <form action="{{ route('checkout.upload', $order->id) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                @csrf
                <label class="upload-zone">
                    <input type="file" name="payment_proof" accept="image/*" required id="proofInput">
                    <div class="upload-zone__inner">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <p class="upload-zone__title">Klik untuk upload bukti</p>
                        <p class="upload-zone__hint">JPG, PNG (maks. 2MB)</p>
                        <p class="upload-zone__filename" id="fileName"></p>
                    </div>
                </label>
                @error('payment_proof')<span class="form-error">{{ $message }}</span>@enderror

                <button type="submit" class="btn-payment">Konfirmasi Pembayaran</button>
            </form>
        </div>
        @endif

        {{-- Upload Bukti — OPSIONAL untuk Bank Transfer (auto-verifikasi via Midtrans) --}}
        @if($order->payment_method === 'bank_transfer')
        <div class="payment-card">
            <h2>Upload Bukti Pembayaran (Opsional)</h2>
            <p>Status pembayaran akan terverifikasi otomatis. Upload bukti ini hanya sebagai cadangan apabila verifikasi otomatis mengalami kendala.</p>

            <form action="{{ route('checkout.upload', $order->id) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                @csrf
                <label class="upload-zone">
                    <input type="file" name="payment_proof" accept="image/*" id="proofInput2">
                    <div class="upload-zone__inner">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <p class="upload-zone__title">Klik untuk upload bukti (opsional)</p>
                        <p class="upload-zone__hint">JPG, PNG (maks. 2MB)</p>
                        <p class="upload-zone__filename" id="fileName2"></p>
                    </div>
                </label>
                @error('payment_proof')<span class="form-error">{{ $message }}</span>@enderror

                <button type="submit" class="btn-payment">Upload Bukti</button>
            </form>
        </div>

        <div class="payment-actions">
            <a href="{{ route('checkout.success') }}?order_id={{ $order->id }}" class="btn-payment" style="display:block;text-align:center;text-decoration:none;">
                Selesai
            </a>
        </div>
        @endif

        {{-- COD: tombol Selesai langsung --}}
        @if($order->payment_method === 'cod')
        <div class="payment-actions">
            <a href="{{ route('checkout.success') }}" class="btn-payment" style="display:block;text-align:center;text-decoration:none;">
                Selesai
            </a>
        </div>
        @endif

<div class="toast" id="toast"></div>

@if($order->payment_method === 'bank_transfer' && $order->payment_status === 'unpaid')
<script>
const rjPaymentPoll = setInterval(() => {
    fetch('{{ route('order.status', $order->id) }}')
        .then(r => r.json())
        .then(data => {
            if (data.payment_status === 'paid') {
                clearInterval(rjPaymentPoll);
                window.location.href = '{{ route('checkout.success') }}?order_id={{ $order->id }}';
            }
        })
        .catch(() => {});
}, 5000);
</script>
@endif

<script>
// Show selected file name
['proofInput', 'proofInput2'].forEach((inputId, i) => {
    const input = document.getElementById(inputId);
    const fileNameId = i === 0 ? 'fileName' : 'fileName2';
    input?.addEventListener('change', function () {
        const fileName = document.getElementById(fileNameId);
        if (this.files[0]) {
            fileName.textContent = '✓ ' + this.files[0].name;
            fileName.style.color = '#5c6b3a';
        }
    });
});

// Copy bank number
document.querySelectorAll('.bank-item__copy').forEach(btn => {
    btn.addEventListener('click', function () {
        const text = this.dataset.copy;
        navigator.clipboard.writeText(text).then(() => {
            this.textContent = 'Tersalin!';
            setTimeout(() => this.textContent = 'Salin', 2000);
        });
    });
});
</script>
@endsection