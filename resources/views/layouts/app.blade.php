<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Retro Junk — Preloved Fashion' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar" id="navbar">
    <a href="{{ route('home') }}" class="navbar__logo {{ request()->routeIs('checkout.*') ? 'navbar__logo--hidden' : '' }}">
    <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="navbar__logo-img">
</a>

    <div class="navbar__links">
        <a href="{{ route('new-arrivals') }}" class="{{ request()->routeIs('new-arrivals') ? 'active' : '' }}">New Arrivals</a>
        <a href="{{ route('category', 'shirts') }}" class="{{ request()->is('category/shirts') ? 'active' : '' }}">Shirts</a>
        <a href="{{ route('category', 'tshirts') }}" class="{{ request()->is('category/tshirts') ? 'active' : '' }}">T-Shirts</a>
        <a href="{{ route('category', 'pants') }}" class="{{ request()->is('category/pants') ? 'active' : '' }}">Pants</a>
        <a href="{{ route('category', 'outerwear') }}" class="{{ request()->is('category/outerwear') ? 'active' : '' }}">Outerwear</a>
    </div>

<div class="navbar__actions">
    <div class="navbar__search-wrapper" id="searchWrapper">
        <form action="{{ route('search') }}" method="GET" class="navbar__search">
            <button type="submit" class="navbar__search-btn" aria-label="Search">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
            <input type="text" 
                   name="q" 
                   class="navbar__search-input" 
                   placeholder="Cari produk..." 
                   autocomplete="off"
                   value="{{ request('q') }}">
        </form>
    </div>

    <button class="icon-btn cart-btn" id="cartToggle" title="Cart">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
        </svg>
        <span class="cart-badge" id="cartCount">{{ count(session()->get('cart', [])) }}</span>
    </button>

    @auth
    <a href="{{ route('account') }}" class="icon-btn" title="Account">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
    </a>
    @else
    <a href="{{ route('login') }}" class="icon-btn" title="Sign In">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
    </a>
    @endauth
</div>

    {{-- Mobile Menu --}}
    <button class="navbar__hamburger" id="mobileMenu">
        <span></span><span></span><span></span>
    </button>
</nav>

{{-- Cart Sidebar --}}
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-sidebar__header">
        <h2>Cart</h2>
        <button class="cart-sidebar__close" id="cartClose">✕</button>
    </div>
    <div class="cart-sidebar__items" id="cartItems">
        @php $cart = session()->get('cart', []); @endphp
        @forelse($cart as $item)
        <div class="cart-item" data-id="{{ $item['id'] }}">
            <div class="cart-item__img">
                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                     onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
            </div>
            <div class="cart-item__info">
                <p class="cart-item__name">{{ $item['name'] }}</p>
                <p class="cart-item__price">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                <button class="cart-item__remove" data-id="{{ $item['id'] }}">Remove</button>
            </div>
        </div>
        @empty
        <p class="cart-empty">Keranjang kamu kosong.</p>
        @endforelse
    </div>
    <div class="cart-sidebar__footer">
        <a href="{{ route('checkout.index') }}" class="btn-checkout">Checkout</a>
    </div>
</div>

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
<footer class="footer">
    <div class="footer__inner">
        <div class="footer__brand">
            <div class="footer__logo">
                <img src="{{ asset('images/logo.png') }}" alt="Retro Junk" class="footer__logo-img">
            </div>
            <p>Preloved fashion berkualitas dengan selera retro yang tak lekang waktu.</p>
        </div>

        <div class="footer__links">
            <div>
                <h4>Koleksi</h4>
                <a href="{{ route('new-arrivals') }}">New Arrivals</a>
                <a href="{{ route('category', 'shirts') }}">Shirts</a>
                <a href="{{ route('category', 'tshirts') }}">T-Shirts</a>
                <a href="{{ route('category', 'pants') }}">Pants</a>
                <a href="{{ route('category', 'outerwear') }}">Outerwear</a>
            </div>
            <div>
                <h4>Informasi</h4>
                <a href="#">Tentang Kami</a>
                <a href="#">Cara Pemesanan</a>
                <a href="#">Kebijakan Pengembalian</a>
                <a href="#">Kontak</a>
            </div>
            <div>
                <h4>Ikuti Kami</h4>
                <a href="#">Instagram</a>
                <a href="#">TikTok</a>
                <a href="#">WhatsApp</a>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <p>&copy; {{ date('Y') }} Retro Junk. All rights reserved.</p>
    </div>
</footer>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
