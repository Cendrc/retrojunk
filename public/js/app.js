// ===================== RETRO JUNK — Main JS =====================

document.addEventListener('DOMContentLoaded', () => {

    // ---- Cart Sidebar ----
    const cartToggle = document.getElementById('cartToggle');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartClose = document.getElementById('cartClose');

    cartToggle?.addEventListener('click', () => {
        cartSidebar.classList.add('open');
        cartOverlay.classList.add('open');
        loadCartItems();
    });

    cartClose?.addEventListener('click', () => {
        cartSidebar.classList.remove('open');
        cartOverlay.classList.remove('open');
    });

    cartOverlay?.addEventListener('click', () => {
        cartSidebar.classList.remove('open');
        cartOverlay.classList.remove('open');
    });

    // ---- Navbar scroll effect ----
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            navbar?.classList.add('scrolled');
        } else {
            navbar?.classList.remove('scrolled');
        }
    });

    // ---- Mobile Menu (hamburger) ----
    const mobileMenuBtn = document.getElementById('mobileMenu');
    const navLinks = document.querySelector('.navbar__links');

    mobileMenuBtn?.addEventListener('click', () => {
        navLinks?.classList.toggle('mobile-open');
        mobileMenuBtn.classList.toggle('active');
    });

    // Tutup menu mobile saat salah satu link diklik
    navLinks?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('mobile-open');
            mobileMenuBtn?.classList.remove('active');
        });
    });

    // Tutup menu mobile saat tap di luar navbar
    document.addEventListener('click', (e) => {
        if (navLinks?.classList.contains('mobile-open') &&
            !navLinks.contains(e.target) &&
            !mobileMenuBtn?.contains(e.target)) {
            navLinks.classList.remove('mobile-open');
            mobileMenuBtn?.classList.remove('active');
        }
    });

    // ---- Navbar Search (tap-to-expand, cegah submit kosong) ----
    const searchWrapper = document.getElementById('searchWrapper');
    const searchInput = searchWrapper?.querySelector('.navbar__search-input');
    const searchBtn = searchWrapper?.querySelector('.navbar__search-btn');

    searchBtn?.addEventListener('click', (e) => {
        const isExpanded = searchWrapper.classList.contains('active');
        const hasValue = searchInput?.value.trim().length > 0;

        if (!isExpanded) {
            // Tap pertama: buka search bar dulu, jangan langsung submit
            e.preventDefault();
            searchWrapper.classList.add('active');
            searchInput?.focus();
        } else if (!hasValue) {
            // Sudah terbuka tapi masih kosong: tetap jangan submit
            e.preventDefault();
            searchInput?.focus();
        }
        // Kalau sudah terbuka DAN ada isi -> biarkan form submit normal
    });

    document.addEventListener('click', (e) => {
        if (searchWrapper?.classList.contains('active') &&
            !searchWrapper.contains(e.target)) {
            searchWrapper.classList.remove('active');
        }
    });

    // ---- Esc to close semua panel ----
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            cartSidebar?.classList.remove('open');
            cartOverlay?.classList.remove('open');
            navLinks?.classList.remove('mobile-open');
            mobileMenuBtn?.classList.remove('active');
            searchWrapper?.classList.remove('active');
        }
    });

    // ---- Product Gallery Dots ----
    const dots = document.querySelectorAll('.dot');
    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
            dots.forEach(d => d.classList.remove('active'));
            dot.classList.add('active');
        });
    });

    // ---- Checkout item remove ----
    document.querySelectorAll('.checkout-item__remove').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.dataset.id;
            removeFromCart(id, () => {
                this.closest('.checkout-item')?.remove();
            });
        });
    });

});

// ---- Load Cart Items (Real-time fetch) ----
function loadCartItems() {
    const cartItems = document.getElementById('cartItems');
    if (!cartItems) return;

    cartItems.innerHTML = '<p class="cart-empty">Loading...</p>';

    fetch('/cart/items')
        .then(r => r.json())
        .then(data => {
            if (data.items && data.items.length > 0) {
                cartItems.innerHTML = data.items.map(item => `
                    <div class="cart-item" data-id="${item.id}">
                        <div class="cart-item__img">
                            <img src="${item.image}" alt="${item.name}"
                                 onerror="this.src='/images/placeholder.jpg'">
                        </div>
                        <div class="cart-item__info">
                            <p class="cart-item__name">${item.name}</p>
                            <p class="cart-item__price">IDR ${Number(item.price).toLocaleString('id-ID')}</p>
                            <button class="cart-item__remove" data-id="${item.id}">Remove</button>
                        </div>
                    </div>
                `).join('');

                cartItems.querySelectorAll('.cart-item__remove').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.id;
                        removeFromCart(id, () => {
                            loadCartItems();
                        });
                    });
                });
            } else {
                cartItems.innerHTML = '<p class="cart-empty">Keranjang kamu kosong.</p>';
            }
        })
        .catch(err => {
            console.error('Failed to load cart:', err);
            cartItems.innerHTML = '<p class="cart-empty">Gagal memuat keranjang.</p>';
        });
}

// ---- Remove from cart ----
function removeFromCart(id, callback) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch('/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ product_id: id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const cartCount = document.getElementById('cartCount');
            if (cartCount) cartCount.textContent = data.count;
            callback?.();
        }
    })
    .catch(err => console.error('Remove error:', err));
}

// ---- Toast notification ----
function showToast(message) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3000);
}

window.showToast = showToast;
window.loadCartItems = loadCartItems;