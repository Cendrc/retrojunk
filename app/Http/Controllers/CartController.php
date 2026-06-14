<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = array_sum(array_column($cart, 'price'));
        return view('pages.cart', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        $cart[$product->id] = [
            'id'    => $product->id,
            'name'  => $product->name,
            'price' => $product->price,
            'image' => $product->image,
            'size'  => $product->size,
        ];

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'count'   => count($cart),
            'message' => $product->name . ' ditambahkan ke keranjang'
        ]);
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        unset($cart[$request->product_id]);
        session()->put('cart', $cart);

        $total = array_sum(array_column($cart, 'price'));

        return response()->json([
            'success' => true,
            'count'   => count($cart),
            'total'   => 'IDR ' . number_format($total, 0, ',', '.'),
        ]);
    }

    public function items()
    {
        $cart = session()->get('cart', []);
        $items = array_values($cart);
        return response()->json([
            'items' => $items,
            'count' => count($items),
            'total' => 'IDR ' . number_format(array_sum(array_column($cart, 'price')), 0, ',', '.'),
        ]);
    }

}