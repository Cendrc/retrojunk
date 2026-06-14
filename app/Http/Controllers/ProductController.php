<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $newArrivals = Product::newArrivals()->latest()->take(5)->get();
        $cargo = Product::category('pants')->latest()->take(5)->get();
        $shirts = Product::category('shirts')->latest()->take(4)->get();
        $tshirts = Product::category('tshirts')->latest()->take(4)->get();

        return view('pages.home', compact('newArrivals', 'cargo', 'shirts', 'tshirts'));
    }

    public function newArrivals()
    {
        $products = Product::newArrivals()->latest()->paginate(12);
        return view('pages.category', [
            'products' => $products,
            'title' => 'New Arrivals',
            'category' => 'new-arrivals'
        ]);
    }

    public function category($category)
    {
        $titles = [
            'shirts' => 'Shirts',
            'tshirts' => 'T-Shirts',
            'pants' => 'Pants',
            'outerwear' => 'Outerwear',
        ];

    // Coming soon categories
        $comingSoon = ['shirts', 'tshirts'];
        if (in_array($category, $comingSoon)) {
            return view('pages.coming-soon', [
                'title' => $titles[$category] ?? ucfirst($category),
            ]);
        }

        $products = Product::category($category)->latest()->paginate(12);
        return view('pages.category', [
            'products' => $products,
            'title' => $titles[$category] ?? ucfirst($category),
            'category' => $category
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $related = Product::category($product->category)
            ->where('id', '!=', $id)
            ->take(4)->get();

        return view('pages.product', compact('product', 'related'));
    }
}
