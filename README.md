# 🎵 Retro Junk — Preloved Fashion E-Commerce

Website toko fashion preloved dengan tampilan retro vintage, dibangun menggunakan **Laravel 11**.

---

## 🎨 Fitur

- **Landing Page** — Hero banner, koleksi cargo, shirts, t-shirts, banner CTA
- **Product Page** — Galeri gambar, detail produk, spesifikasi, Add to Cart
- **Cart Sidebar** — Sidebar animasi dengan daftar item dan remove
- **Cart Page** — Halaman keranjang penuh dengan ringkasan pesanan
- **Checkout Page** — Form kontak & pengiriman, alamat tersimpan, ringkasan order
- **Pembayaran** — Integrasi Midtrans (bank transfer) + opsi QRIS manual
- **Kategori** — New Arrivals, Shirts, T-Shirts, Pants, Outerwear
- **Pencarian** — Search produk real-time
- **Autentikasi** — Register, Login, Logout
- **Admin Panel** — Kelola produk (CRUD + upload gambar), pesanan, dan pelanggan
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

# 6. Isi kredensial akun admin di .env (dipakai oleh AdminUserSeeder)
ADMIN_NAME="Admin Retro Junk"
ADMIN_EMAIL=admin@retrojunk.id
ADMIN_PASSWORD=isi_password_admin_disini

# 7. Buat database di MySQL
mysql -u root -p -e "CREATE DATABASE retrojunk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 8. Jalankan migrasi + seed (otomatis mengisi katalog produk lengkap
#    dan membuat/update akun admin sesuai .env di atas)
php artisan migrate --seed

# 9. Link storage (untuk upload bukti pembayaran)
php artisan storage:link

# 10. Jalankan server
php artisan serve
```

Buka `http://localhost:8000` di browser. Login admin panel di `/admin` memakai `ADMIN_EMAIL` / `ADMIN_PASSWORD` yang sudah diisi.

> Setiap kali ada produk baru yang ditambahkan lewat admin panel dan ingin ikut terbawa ke deployment berikutnya, tambahkan juga datanya ke `database/seeders/ProductSeeder.php` (seeder ini idempotent — aman dijalankan ulang, tidak akan menduplikasi produk yang sudah ada).

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
│   ├── migrations/                 # Tabel products, orders, order_items, addresses, dll
│   └── seeders/
│       ├── ProductSeeder.php       # Katalog produk (idempotent, sinkron dgn data admin panel)
│       └── AdminUserSeeder.php     # Akun admin, dibuat dari ADMIN_* di .env
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

Cara utama: lewat **Admin Panel** (`/admin/products`) — login dengan akun admin, lalu tambah/edit/hapus produk beserta upload gambarnya. Gambar otomatis tersimpan di `public/images/products/`.

Supaya produk yang ditambahkan lewat admin panel ikut terbawa saat deploy ulang / ke server baru, tambahkan juga entrinya ke `database/seeders/ProductSeeder.php` (array `$products`), lalu commit & push perubahannya. Seeder ini pakai `updateOrCreate` berdasarkan `slug`, jadi aman dijalankan berkali-kali tanpa membuat data duplikat.

---

## 🔒 Lisensi

MIT License — bebas digunakan dan dimodifikasi.
