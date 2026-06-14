@extends('layouts.app')
@section('content')

<section class="category-page">
    <div class="category-page__header">
        <h1>{{ $title }}</h1>
        <p>{{ $products->total() }} item ditemukan</p>
    </div>

    @if($products->count())
    <div class="product-grid product-grid--full">
        @foreach($products as $product)
        <a href="{{ route('product.show', $product->id) }}" class="product-card">
            <div class="product-card__img">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                @if($product->is_new_arrival)
                <span class="badge-new">New</span>
                @endif
            </div>
            <div class="product-card__info">
                <p class="product-card__name">{{ $product->name }}</p>
                <p class="product-card__price">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
        </a>
        @endforeach
    </div>

    <div class="pagination-wrap">
        {{ $products->links() }}
    </div>
    @else
    <div class="empty-state">
        <p>Belum ada produk di kategori ini.</p>
        <a href="{{ route('home') }}" class="btn-outline">Kembali ke Beranda</a>
    </div>
    @endif
</section>

@endsection
