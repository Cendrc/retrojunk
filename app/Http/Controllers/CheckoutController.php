<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Helpers\ShippingCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home');
        }
        $total = array_sum(array_column($cart, 'price'));
        return view('pages.checkout', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Keranjang kosong.');
        }
        
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
        ]);

        // ==========================================================
        // STOCK LOCK MECHANISM
        // Gunakan DB transaction + lockForUpdate untuk mencegah
        // race condition / pemesanan ganda pada produk stok satuan.
        // ==========================================================
        try {
            $order = DB::transaction(function () use ($request, $cart) {
                
                // STEP 1: Lock semua produk di cart & cek stok
                $productIds = array_column($cart, 'id');
                $products = Product::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                
                // STEP 2: Validasi stok setiap produk
                foreach ($cart as $item) {
                    $product = $products->get($item['id']);
                    
                    if (!$product) {
                        throw new \Exception("Produk '{$item['name']}' tidak ditemukan.");
                    }
                    
                    if ($product->stock < 1) {
                        // Custom exception dengan info produk yang habis
                        $exception = new \Exception("Stok produk '{$product->name}' sudah habis.");
                        $exception->productId = $product->id;
                        $exception->productSlug = $product->slug;
                        $exception->productName = $product->name;
                        throw $exception;
                    }
                }
                
                // STEP 3: Hitung total pembayaran
                $subtotal = array_sum(array_column($cart, 'price'));
                $shipping = ShippingCalculator::calculate(
                    $request->province, 
                    $request->city, 
                    $subtotal
                );
                $shippingCost = $shipping['cost'];
                $total = $subtotal + $shippingCost;
                
                // STEP 4: Buat Order
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'session_id' => session()->getId(),
                    'email' => $request->email,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'province' => $request->province,
                    'city' => $request->city,
                    'district' => $request->district,
                    'postal_code' => $request->postal_code,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'shipping_zone' => $shipping['name'],
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'order_status' => 'pending',
                    'payment_status' => 'unpaid',
                ]);
                
                // STEP 5: Buat order_items + kurangi stok produk
                // Track produk yang sudah diproses untuk mencegah duplikasi
                $processedProductIds = [];

                foreach ($cart as $item) {
                    // Skip kalau produk sudah diproses (safety)
                    if (in_array($item['id'], $processedProductIds)) {
                        continue;
                    }
                    $processedProductIds[] = $item['id'];
                    
                    $product = $products->get($item['id']);
                    
                    // Kurangi stok (dari 1 jadi 0)
                    $product->decrement('stock', 1);
                    
                    // Buat order_item dengan snapshot data produk
                    $order->items()->create([
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'product_price' => $item['price'],
                        'product_image' => $item['image'] ?? null,
                        'quantity' => 1,
                        'subtotal' => $item['price'],
                    ]);
                }
                
                return $order;
            });
            
            // TRANSACTION COMMIT: Bersihkan cart & redirect ke payment
            session()->forget('cart');
            session()->flash('order_id', $order->id);
            
            return redirect()->route('checkout.payment', $order->id);
            
        } catch (\Exception $e) {
            Log::warning('Checkout failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'cart' => $cart,
            ]);
            
            // Kalau exception ada info produk (stok habis), redirect ke halaman produk
            if (isset($e->productId)) {
                // Hapus produk yang habis dari cart
                $cart = session()->get('cart', []);
                unset($cart[$e->productId]);
                session()->put('cart', $cart);
                
                // Redirect ke halaman detail produk dengan flash notification
                return redirect()->route('product.show', $e->productId)
                    ->with('stock_error', [
                        'product_name' => $e->productName,
                        'message' => $e->getMessage(),
                    ]);
            }
            
            // Fallback: redirect ke home dengan error umum
            return redirect()->route('home')
                ->with('error', $e->getMessage());
        }
    }

    public function payment($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('pages.payment', compact('order'));
    }

    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $order = Order::with('items.product')->findOrFail($id);
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_status' => 'awaiting_verification',
        ]);

        return redirect()->route('checkout.success')->with('order_id', $order->id);
    }

    public function success(Request $request)
    {
        $order = null;
        if (session('order_id')) {
            $order = Order::with('items.product')->find(session('order_id'));
        }
        return view('pages.checkout-success', compact('order'));
    }
}