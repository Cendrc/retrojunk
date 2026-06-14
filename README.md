# 🎵 Retro Junk — Preloved Fashion E-Commerce

Website toko fashion preloved dengan tampilan retro vintage, dibangun menggunakan **Laravel 11**.

---

## 🎨 Fitur

- **Landing Page** — Hero banner, koleksi cargo, shirts, t-shirts, banner CTA
- **Product Page** — Galeri gambar, detail produk, spesifikasi, Add to Cart
- **Cart Sidebar** — Sidebar animasi dengan daftar item dan remove
- **Cart Page** — Halaman keranjang penuh dengan ringkasan pesanan
- **Checkout Page** — Form kontak & pengiriman + ringkasan order
- **Kategori** — New Arrivals, Shirts, T-Shirts, Pants, Outerwear
- **Pencarian** — Search produk real-time
- **Autentikasi** — Register, Login, Logout
- **Session Cart** — Keranjang berbasis session (tanpa login)
- **Responsive** — Mobile-friendly

---

## 🛠️ Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- MySQL / MariaDB
- Node.js & NPM

### Langkah-langkah

```bash
# 1. Clone / salin project
cd retrojunk

# 2. Install dependensi PHP
composer install

# 3. Salin file .env
cp .env.example .env

# 4. Generate App Key
php artisan key:generate

# 5. Konfigurasi database di .env
DB_DATABASE=retrojunk
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Buat database di MySQL
mysql -u root -p -e "CREATE DATABASE retrojunk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Jalankan migrasi
php artisan migrate

# 8. Seed data produk awal
php artisan db:seed

# 9. Link storage (untuk upload gambar)
php artisan storage:link

# 10. Jalankan server
php artisan serve
```

Buka `http://localhost:8000` di browser.

---

## 📁 Struktur Utama

```
retrojunk/
├── app/
│   ├── Http/Controllers/
│   │   ├── ProductController.php   # Landing, kategori, detail produk
│   │   ├── CartController.php      # Keranjang (add, remove, count)
│   │   ├── CheckoutController.php  # Checkout & sukses
│   │   ├── AuthController.php      # Login, register, logout
│   │   └── SearchController.php    # Pencarian produk
│   └── Models/
│       ├── Product.php
│       └── Order.php
├── database/
│   ├── migrations/                 # Tabel products & orders
│   └── seeders/ProductSeeder.php   # 19 produk sample
├── resources/views/
│   ├── layouts/app.blade.php       # Layout utama (navbar, cart sidebar, footer)
│   └── pages/
│       ├── home.blade.php          # Landing page
│       ├── product.blade.php       # Detail produk
│       ├── category.blade.php      # Halaman kategori
│       ├── cart.blade.php          # Halaman keranjang
│       ├── checkout.blade.php      # Halaman checkout
│       ├── checkout-success.blade.php
│       ├── search.blade.php
│       └── auth/login.blade.php & register.blade.php
├── public/
│   ├── css/app.css                 # Semua styling (olive green theme)
│   ├── js/app.js                   # Interaksi (cart, search, toast)
│   └── images/                     # Gambar produk (upload manual)
└── routes/web.php                  # Semua route
```

---

## 🖼️ Upload Gambar Produk

Letakkan gambar produk di folder `public/images/` dengan format:
- `hero-banner.jpg` — Gambar hero landing page
- `placeholder.jpg` — Gambar default produk
- `cargo-carfarmall.jpg`, dll

Update kolom `image` di database atau di `ProductSeeder.php`.

---

## 🎨 Warna Tema

| Variable       | Warna       | Hex       |
|----------------|-------------|-----------|
| `--olive`      | Hijau Olive | `#5c6b3a` |
| `--olive-dark` | Olive Gelap | `#4a5630` |
| `--brown`      | Coklat Tua  | `#3d2c1e` |
| `--cream`      | Krem        | `#f0ebe1` |

---

## 📦 Menambah Produk

Via Tinker:
```bash
php artisan tinker

App\Models\Product::create([
    'name' => 'Nama Produk',
    'slug' => 'nama-produk',
    'price' => 150000,
    'category' => 'pants', // shirts | tshirts | pants | outerwear
    'size' => '30/M',
    'waist_size' => '76 cm',
    'length' => '98 cm',
    'code' => 'CG-20',
    'image' => '/images/nama-produk.jpg',
    'stock' => 1,
    'is_new_arrival' => true,
]);
```

---

## 🔒 Lisensi

MIT License — bebas digunakan dan dimodifikasi.
