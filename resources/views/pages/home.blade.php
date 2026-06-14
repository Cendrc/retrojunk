@extends('layouts.app')
@section('content')

{{-- HERO BANNER --}}
<section class="hero">
    <div class="hero__text">
        <h1>NEW<br>ARRIVALS</h1>
    </div>
    <div class="hero__img">
        <img src="{{ asset('images/hero-banner.png') }}" alt="New Arrivals"
             onerror="this.style.display='none'">
    </div>
</section>

{{-- CARGO COLLECTIONS --}}
<section class="collections">
    <h2 class="section-title">Cargo Collections</h2>
    <div class="product-grid">
        @foreach($cargo as $product)
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
</section>

{{-- FEATURED OUTERWEAR --}}
@php
    $outerwear = \App\Models\Product::category('outerwear')->latest()->take(2)->get();
@endphp

@if($outerwear->count())
<section class="collections collections--alt">
    <h2 class="section-title">Featured Outerwear</h2>
    <div class="product-grid product-grid--featured">
        @foreach($outerwear as $product)
        <a href="{{ route('product.show', $product->id) }}" class="product-card product-card--featured">
            <div class="product-card__img">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
                <span class="badge-featured">Featured</span>
            </div>
            <div class="product-card__info">
                <p class="product-card__name">{{ $product->name }}</p>
                <p class="product-card__price">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
        </a>
        @endforeach
    </div>
    <div class="section-cta">
        <a href="{{ route('category', 'outerwear') }}" class="btn-outline">Lihat Semua Outerwear →</a>
    </div>
</section>
@endif

{{-- BANNER CTA --}}
<section class="banner-cta">
    <div class="banner-cta__inner">
        <h2>Temukan Gaya Retro Kamu</h2>
        <p>Koleksi preloved pilihan dengan kondisi terbaik, harga bersahabat.</p>
        <a href="{{ route('new-arrivals') }}" class="btn-primary">Jelajahi Koleksi</a>
    </div>
</section>

@endsection