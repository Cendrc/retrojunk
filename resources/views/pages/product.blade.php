@extends('layouts.app')
@section('content')

{{-- ============================================
     STOCK ERROR MODAL (Fitur Stock Lock)
     Muncul otomatis kalau ada session stock_error
     ============================================ --}}
@if(session('stock_error'))
<div class="stock-modal" id="stockModal">
    <div class="stock-modal__overlay" onclick="closeStockModal()"></div>
    <div class="stock-modal__content">
        <button class="stock-modal__close" onclick="closeStockModal()" aria-label="Tutup">✕</button>
        
        <div class="stock-modal__icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        
        <h2 class="stock-modal__title">Yah, Kehabisan Stok</h2>
        
        <p class="stock-modal__message">
            Produk <strong>"{{ session('stock_error')['product_name'] }}"</strong> baru saja dipesan customer lain.
        </p>
        
        <p class="stock-modal__sub">
            Karena ini produk preloved dengan stok satuan, hanya satu customer yang beruntung. Yuk cek produk serupa di bawah yang masih tersedia!
        </p>
        
        <div class="stock-modal__actions">
            <button class="stock-modal__btn stock-modal__btn--primary" onclick="closeStockModal()">
                Lihat Produk Lain
            </button>
        </div>
    </div>
</div>
@endif

<section class="product-detail">
    {{-- Product Image Gallery --}}
    <div class="product-detail__gallery">
        <div class="gallery__main" id="mainImg">
            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                 id="mainImage" onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
        </div>
        <div class="gallery__dots" id="galleryDots">
            <span class="dot active" data-index="0"></span>
            @if($product->images)
                @foreach($product->images as $i => $img)
                <span class="dot" data-index="{{ $i + 1 }}"></span>
                @endforeach
            @else
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            @endif
        </div>
    </div>

    {{-- Product Info --}}
    <div class="product-detail__info">
        <h1 class="product-detail__name">{{ $product->name }}</h1>
        <hr class="product-detail__divider">

        <p class="product-detail__price">IDR {{ number_format($product->price, 0, ',', '.') }}</p>

        <div class="product-detail__specs">
            <p class="specs-label">Product details:</p>
            <ul>
                @if($product->size)
                <li><span>Size</span> {{ $product->size }}</li>
                @endif
                {{-- Pants/Celana --}}
                @if($product->waist_size)
                <li><span>Waist size:</span> {{ $product->waist_size }}</li>
                @endif
                @if($product->length)
                <li><span>Length:</span> {{ $product->length }}</li>
                @endif
                @if($product->open_leg)
                <li><span>Open Leg:</span> {{ $product->open_leg }}</li>
                @endif
                {{-- Outerwear/Jacket --}}
                @if($product->chest_width)
                <li><span>Lebar:</span> {{ $product->chest_width }}</li>
                @endif
                @if($product->body_length)
                <li><span>Panjang:</span> {{ $product->body_length }}</li>
                @endif
                @if($product->sleeve_length)
                <li><span>Panjang Lengan:</span> {{ $product->sleeve_length }}</li>
                @endif
                @if($product->code)
                <li><span>Code:</span> {{ $product->code }}</li>
                @endif
            </ul>
        </div>

        @if($product->description)
        <p class="product-detail__desc">{{ $product->description }}</p>
        @endif

        @if($product->stock > 0)
        <button class="btn-add-cart" id="addToCart" data-id="{{ $product->id }}">
            Add to cart
        </button>
        @else
        <button class="btn-add-cart btn-add-cart--sold" disabled>Sold Out</button>
        @endif

        <div class="product-detail__meta">
            <span>Stok: {{ $product->stock }} tersisa</span>
            <span>Kode: {{ $product->code }}</span>
        </div>
    </div>
</section>

{{-- Related Products --}}
@if($related->count())
<section class="related-products">
    <h2 class="section-title">Produk Serupa</h2>
    <div class="product-grid">
        @foreach($related as $item)
        <a href="{{ route('product.show', $item->id) }}" class="product-card">
            <div class="product-card__img">
                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
            </div>
            <div class="product-card__info">
                <p class="product-card__name">{{ $item->name }}</p>
                <p class="product-card__price">IDR {{ number_format($item->price, 0, ',', '.') }}</p>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- Toast notification --}}
<div class="toast" id="toast"></div>

<script>
document.getElementById('addToCart')?.addEventListener('click', function () {
    const id = this.dataset.id;
    fetch('{{ route('cart.add') }}', {
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
            document.getElementById('cartCount').textContent = data.count;
            showToast(data.message);
        }
    });
});

// ============================================
// STOCK MODAL - Close Function
// ============================================
function closeStockModal() {
    const modal = document.getElementById('stockModal');
    if (modal) {
        modal.classList.add('stock-modal--closing');
        setTimeout(() => modal.remove(), 300);
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStockModal();
    }
});
</script>
@endsection