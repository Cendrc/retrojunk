@extends('layouts.app')
@section('content')

<section class="cart-page">
    <h1 class="cart-page__title">Keranjang Belanja</h1>

    @if(count($cart) > 0)
    <div class="cart-page__layout">
        <div class="cart-page__items">
            @foreach($cart as $item)
            <div class="cart-page__item" id="cart-item-{{ $item['id'] }}">
                <div class="cart-page__item-img">
                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                </div>
                <div class="cart-page__item-info">
                    <p class="cart-page__item-name">{{ $item['name'] }}</p>
                    <p class="cart-page__item-size">{{ $item['size'] ?? '' }}</p>
                    <p class="cart-page__item-price">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                </div>
                <button class="cart-page__remove" data-id="{{ $item['id'] }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>

        <div class="cart-page__summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="cartTotal">IDR {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row summary-row--note">
                <span>Ongkos kirim dihitung saat checkout</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-checkout-full">
                Lanjut ke Checkout
            </a>
            <a href="{{ route('home') }}" class="btn-continue">← Lanjut Belanja</a>
        </div>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state__icon">🛍️</div>
        <h2>Keranjang kamu kosong</h2>
        <p>Temukan produk favoritmu dan mulai belanja!</p>
        <a href="{{ route('home') }}" class="btn-primary">Mulai Belanja</a>
    </div>
    @endif
</section>

<script>
document.querySelectorAll('.cart-page__remove').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        fetch('{{ route('cart.remove') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: id })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-item-' + id)?.remove();
                document.getElementById('cartCount').textContent = data.count;
                document.getElementById('cartTotal').textContent = data.total;
                if (data.count === 0) location.reload();
            }
        });
    });
});
</script>
@endsection
