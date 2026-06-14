// ===================== RETRO JUNK — Main JS =====================

document.addEventListener('DOMContentLoaded', () => {

    // ---- Search Toggle ----
    const searchToggle = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.getElementById('searchClose');

    searchToggle?.addEventListener('click', () => {
        searchOverlay.classList.toggle('open');
        if (searchOverlay.classList.contains('open')) {
            const searchInput = searchOverlay.querySelector('input');
            if (searchInput) {
                searchInput.value = '';  // Clear isi input
            }
            setTimeout(() => searchInput?.focus(), 100);
        }
    });

    searchClose?.addEventListener('click', () => {
        searchOverlay.classList.remove('open');
        const searchInput = searchOverlay.querySelector('input');
        if (searchInput) searchInput.value = '';
    });

    // ---- Cart Sidebar ----
    const cartToggle = document.getElementById('cartToggle');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const cartClose = document.getElementById('cartClose');

    cartToggle?.addEventListener('click', () => {
        cartSidebar.classList.add('open');
        cartOverlay.classList.add('open');
        loadCartItems(); // Fetch items terbaru setiap buka sidebar
    });

    cartClose?.addEventListener('click', () => {
        cartSidebar.classList.remove('open');
        cartOverlay.classList.remove('open');
    });

    cartOverlay?.addEventListener('click', () => {
        cartSidebar.classList.remove('open');
        cartOverlay.classList.remove('open');
    });

    // ---- Esc to close ----
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            searchOverlay?.classList.remove('open');
            cartSidebar?.classList.remove('open');
            cartOverlay?.classList.remove('open');
            const searchInput = searchOverlay?.querySelector('input');
            if (searchInput) searchInput.value = '';
        }
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

    // ---- Mobile Menu ----
    const mobileMenuBtn = document.getElementById('mobileMenu');
    const navLinks = document.querySelector('.navbar__links');

    mobileMenuBtn?.addEventListener('click', () => {
        navLinks?.classList.toggle('mobile-open');
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

                // Attach remove listeners
                cartItems.querySelectorAll('.cart-item__remove').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = this.dataset.id;
                        removeFromCart(id, () => {
                            loadCartItems(); // Reload sidebar
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