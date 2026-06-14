@extends('layouts.app')
@section('content')

<section class="category-page">
    <div class="category-page__header">
        <h1>Hasil pencarian: "{{ $query }}"</h1>
        <p>{{ $products->total() }} item ditemukan</p>
    </div>

    @if($products->count())
    <div class="product-grid product-grid--full">
        @foreach($products as $product)
        <a href="{{ route('product.show', $product->id) }}" class="product-card">
            <div class="product-card__img">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
            </div>
            <div class="product-card__info">
                <p class="product-card__name">{{ $product->name }}</p>
                <p class="product-card__price">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
        </a>
        @endforeach
    </div>
    <div class="pagination-wrap">{{ $products->links() }}</div>
    @else
    <div class="empty-state">
        <p>Produk "{{ $query }}" tidak ditemukan.</p>
        <a href="{{ route('home') }}" class="btn-outline">Kembali ke Beranda</a>
    </div>
    @endif
</section>
@endsection
