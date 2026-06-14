<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helpers\ShippingCalculator;
use Illuminate\Http\Request;

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
        'email'          => 'required|email',
        'name'           => 'required|string|max:255',
        'phone'          => 'required|string|regex:/^[0-9]{10,15}$/',
        'address'        => 'required|string',
        'province'       => 'required|string',
        'city'           => 'required|string',
        'district'       => 'required|string',
        'postal_code'    => 'required|string|regex:/^[0-9]{5}$/',
        'payment_method' => 'required|in:qris,bank_transfer,cod',
    ]);

    $subtotal = array_sum(array_column($cart, 'price'));
    
    // Hitung ongkos kirim
    $shipping = ShippingCalculator::calculate($request->province, $request->city, $subtotal);
    $shippingCost = $shipping['cost'];
    $total = $subtotal + $shippingCost;
    
    $status = $request->payment_method === 'cod' ? 'confirmed' : 'awaiting_payment';

    $order = Order::create([
        'user_id'        => auth()->id(),
        'session_id'     => session()->getId(),
        'email'          => $request->email,
        'name'           => $request->name,
        'phone'          => $request->phone,
        'address'        => $request->address,
        'province'       => $request->province,
        'city'           => $request->city,
        'district'       => $request->district,
        'postal_code'    => $request->postal_code,
        'items'          => $cart,
        'subtotal'       => $subtotal,
        'shipping_cost'  => $shippingCost,
        'shipping_zone'  => $shipping['name'],
        'total'          => $total,
        'payment_method' => $request->payment_method,
        'status'         => $status,
    ]);

    session()->forget('cart');

    session()->flash('order_id', $order->id);
    return redirect()->route('checkout.payment', $order->id);
}   

    public function payment($id)
    {
        $order = Order::findOrFail($id);
        return view('pages.payment', compact('order'));
    }

    public function uploadProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $order = Order::findOrFail($id);
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'paid',
        ]);

        return redirect()->route('checkout.success')->with('order_id', $order->id);
    }

    public function success(Request $request)
    {
        $order = null;
        if (session('order_id')) {
            $order = Order::find(session('order_id'));
        }
        return view('pages.checkout-success', compact('order'));
    }
}