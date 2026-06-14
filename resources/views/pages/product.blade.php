@extends('layouts.app')
@section('content')

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
</script>
@endsection