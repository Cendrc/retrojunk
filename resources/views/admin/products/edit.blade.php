@extends('admin.layout', ['title' => 'Edit Produk'])
@section('content')

<div class="admin-back">
    <a href="{{ route('admin.products.index') }}">← Kembali ke Daftar Produk</a>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Nama Produk *</label>
                    <input type="text" name="name" class="admin-input" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="admin-form-group">
                    <label>Kode Produk</label>
                    <input type="text" name="code" class="admin-input" value="{{ old('code', $product->code) }}">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Kategori *</label>
                    <select name="category" class="admin-input" required>
                        <option value="shirts" @selected($product->category === 'shirts')>Shirts</option>
                        <option value="tshirts" @selected($product->category === 'tshirts')>T-Shirts</option>
                        <option value="pants" @selected($product->category === 'pants')>Pants</option>
                        <option value="outerwear" @selected($product->category === 'outerwear')>Outerwear</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label>Harga (IDR) *</label>
                    <input type="number" name="price" class="admin-input" value="{{ old('price', $product->price) }}" min="0" required>
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Size</label>
                    <input type="text" name="size" class="admin-input" value="{{ old('size', $product->size) }}">
                </div>

                <div class="admin-form-group">
                    <label>Stok *</label>
                    <input type="number" name="stock" class="admin-input" value="{{ old('stock', $product->stock) }}" min="0" required>
                </div>
            </div>

            <div class="admin-form-group">
                <label>Path Gambar</label>
                <input type="text" name="image" class="admin-input" value="{{ old('image', $product->image) }}">
                @if($product->image)
                <img src="{{ asset($product->image) }}" alt="Preview" style="margin-top: 0.5rem; max-width: 150px; border-radius: 8px;"
                     onerror="this.style.display='none'">
                @endif
            </div>

            <h3 style="margin-top: 2rem;">Detail Ukuran</h3>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Waist Size</label>
                    <input type="text" name="waist_size" class="admin-input" value="{{ old('waist_size', $product->waist_size) }}">
                </div>

                <div class="admin-form-group">
                    <label>Length</label>
                    <input type="text" name="length" class="admin-input" value="{{ old('length', $product->length) }}">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Open Leg</label>
                    <input type="text" name="open_leg" class="admin-input" value="{{ old('open_leg', $product->open_leg) }}">
                </div>

                <div class="admin-form-group">
                    <label>Chest Width</label>
                    <input type="text" name="chest_width" class="admin-input" value="{{ old('chest_width', $product->chest_width) }}">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Body Length</label>
                    <input type="text" name="body_length" class="admin-input" value="{{ old('body_length', $product->body_length) }}">
                </div>

                <div class="admin-form-group">
                    <label>Sleeve Length</label>
                    <input type="text" name="sleeve_length" class="admin-input" value="{{ old('sleeve_length', $product->sleeve_length) }}">
                </div>
            </div>

            <div class="admin-form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="admin-input" rows="4">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="admin-form-group">
                <label>
                    <input type="checkbox" name="is_new_arrival" value="1" {{ $product->is_new_arrival ? 'checked' : '' }}>
                    Tampilkan di "New Arrivals"
                </label>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

@endsection