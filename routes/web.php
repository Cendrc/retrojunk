<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\ShippingController;

// Landing / Home
Route::get('/', [ProductController::class, 'index'])->name('home');

// Tentang Kami
Route::view('/tentang-kami', 'pages.about')->name('about');

// Category pages
Route::get('/new-arrivals', [ProductController::class, 'newArrivals'])->name('new-arrivals');
Route::get('/category/{category}', [ProductController::class, 'category'])->name('category');

// Product
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('/cart/items', [CartController::class, 'items'])->name('cart.items');

Route::middleware('auth')->group(function () {
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/addresses/{id}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
});

// Midtrans webhook — dipanggil server-to-server oleh Midtrans, TIDAK punya sesi
// login Laravel, jadi harus di luar middleware 'auth' (CSRF exception sudah
// diatur terpisah di bootstrap/app.php).
Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])->name('midtrans.notification');

// Checkout
// Checkout - hanya untuk user yang sudah login
Route::middleware('auth')->group(function () {
    Route::get('/orders/{id}/status', [CheckoutController::class, 'checkStatus'])->name('order.status');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/payment/{id}/upload', [CheckoutController::class, 'uploadProof'])->name('checkout.upload');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Shipping (proxy ke RajaOngkir — API key tetap di server)
    Route::get('/shipping/search', [ShippingController::class, 'search'])->name('shipping.search');
    Route::post('/shipping/cost', [ShippingController::class, 'cost'])->name('shipping.cost');
});

// Tracking
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'search'])->name('tracking.search');
Route::get('/track/{tracking_code}', [TrackingController::class, 'show'])->name('tracking.show');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forgot Password
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'checkEmail'])->name('password.check');
Route::get('/reset-password/{email}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// User Account
Route::middleware('auth')->group(function () {
    Route::get('/account', [AuthController::class, 'account'])->name('account');
    Route::get('/orders', [AuthController::class, 'orders'])->name('orders');
    Route::post('/orders/{id}/cancel', [AuthController::class, 'cancelOrder'])->name('orders.cancel');
});

// ===================== ADMIN ROUTES =====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}', [AdminOrderController::class, 'update'])->name('orders.update');
    
    // Products
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    
    // Customers
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');

    // Admin accounts
    Route::get('/admins', [AdminManagementController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [AdminManagementController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminManagementController::class, 'store'])->name('admins.store');
    Route::delete('/admins/{id}', [AdminManagementController::class, 'destroy'])->name('admins.destroy');
});