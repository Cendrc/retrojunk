<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\MidtransService;
use App\Services\RajaOngkirService;
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

    public function store(Request $request, RajaOngkirService $rajaOngkir)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Keranjang kosong.');
        }

        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string',
            'destination_id' => 'required|integer',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string',
            'courier' => 'required|string',
            'shipping_service' => 'required|string',
        ]);

        // ==========================================================
        // STOCK LOCK MECHANISM
        // Gunakan DB transaction + lockForUpdate untuk mencegah
        // race condition / pemesanan ganda pada produk stok satuan.
        // ==========================================================
        try {
            $order = DB::transaction(function () use ($request, $cart, $rajaOngkir) {

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

                // STEP 3: Hitung total pembayaran. Ongkir DIHITUNG ULANG di
                // server (bukan percaya nilai dari form) supaya tidak bisa
                // dimanipulasi lewat hidden input di browser.
                $subtotal = array_sum(array_column($cart, 'price'));
                $weight = Product::totalWeightFor($productIds);

                $options = $rajaOngkir->calculateCost(
                    (int) config('rajaongkir.origin_id'),
                    (int) $request->destination_id,
                    $weight
                );

                $chosen = collect($options)->first(fn ($opt) =>
                    strcasecmp($opt['courier_code'], $request->courier) === 0
                    && strcasecmp($opt['service'], $request->shipping_service) === 0
                );

                if (!$chosen) {
                    throw new \Exception('Ongkos kirim yang dipilih sudah tidak tersedia. Silakan pilih ulang kurir.');
                }

                $shippingCost = $chosen['cost'];
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
                    'subdistrict' => $request->subdistrict,
                    'postal_code' => $request->postal_code,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'shipping_zone' => $chosen['courier_name'] . ' - ' . $chosen['service'],
                    'courier' => $chosen['courier_code'],
                    'shipping_service' => $chosen['service'],
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

            // Simpan alamat baru jika pengguna login, memilih isi manual, dan mencentang "simpan alamat"
            if (auth()->check() && $request->boolean('save_address') && $request->input('address_choice') === 'new') {
                $user = auth()->user();
                if ($user->addresses()->count() < 10) {
                    $user->addresses()->create([
                        'label' => 'Alamat',
                        'name' => $request->name,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'province' => $request->province,
                        'city' => $request->city,
                        'district' => $request->district,
                        'subdistrict' => $request->subdistrict,
                        'postal_code' => $request->postal_code,
                        'is_default' => $user->addresses()->count() === 0,
                    ]);
                }
            }

            session()->flash('order_id', $order->id);
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

    public function payment($id, MidtransService $midtrans)
    {
        $order = Order::with('items.product')
            ->where('id', $id)
            ->where(function ($q) {
                $q->where('email', auth()->user()->email)
                  ->orWhere('user_id', auth()->id());
            })
            ->firstOrFail();

        if ($order->payment_method === 'bank_transfer' && !$order->midtrans_transaction_id) {
            try {
                $order = $midtrans->chargeBankTransfer($order, 'bca');
            } catch (\Exception $e) {
                Log::error('Midtrans charge failed: ' . $e->getMessage());
                return redirect()->route('home')->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
            }
        }

        return view('pages.payment', compact('order'));
    }

    public function checkStatus($id)
    {
        $order = Order::where('id', $id)
            ->where(function ($q) {
                $q->where('email', auth()->user()->email)
                  ->orWhere('user_id', auth()->id());
            })
            ->firstOrFail();

        return response()->json(['payment_status' => $order->payment_status]);
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
        $orderId = session('order_id') ?? $request->query('order_id');
        $order = $orderId ? Order::with('items.product')->find($orderId) : null;
        return view('pages.checkout-success', compact('order'));
    }
}