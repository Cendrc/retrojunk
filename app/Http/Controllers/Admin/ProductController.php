<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:shirts,tshirts,pants,outerwear',
            'size' => 'nullable|string',
            'code' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'is_new_arrival' => 'boolean',
        ]);

        $data = $request->except(['_token']);
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['is_new_arrival'] = $request->has('is_new_arrival');

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:shirts,tshirts,pants,outerwear',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        $data['is_new_arrival'] = $request->has('is_new_arrival');

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}