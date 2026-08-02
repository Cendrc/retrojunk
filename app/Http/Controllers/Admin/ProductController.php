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
            'weight' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'is_new_arrival' => 'boolean',
        ]);

        $data = $request->except(['_token', 'image']);
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['is_new_arrival'] = $request->has('is_new_arrival');

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeProductImage($request->file('image'));
        }

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
            'weight' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $data = $request->except(['_token', '_method', 'image']);
        $data['is_new_arrival'] = $request->has('is_new_arrival');

        if ($request->hasFile('image')) {
            // Hapus gambar lama supaya tidak menumpuk file tak terpakai
            $this->deleteProductImage($product->image);
            $data['image'] = $this->storeProductImage($request->file('image'));
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->deleteProductImage($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Simpan file gambar produk ke public/images/products dan
     * kembalikan path relatif untuk disimpan di kolom `image`.
     */
    private function storeProductImage($file): string
    {
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '-' . time() . '.' . $file->getClientOriginalExtension();

        $destination = public_path('images/products');
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        return 'images/products/' . $filename;
    }

    /**
     * Hapus file gambar lama dari public/images/products, jika ada.
     */
    private function deleteProductImage(?string $path): void
    {
        if (!$path) return;

        $fullPath = public_path($path);
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}