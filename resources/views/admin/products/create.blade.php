@extends('admin.layout', ['title' => 'Tambah Produk'])
@section('content')

<div class="admin-back">
    <a href="{{ route('admin.products.index') }}">← Kembali ke Daftar Produk</a>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Nama Produk *</label>
                    <input type="text" name="name" class="admin-input" value="{{ old('name') }}" required>
                    @error('name') <span class="admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="admin-form-group">
                    <label>Kode Produk</label>
                    <input type="text" name="code" class="admin-input" value="{{ old('code') }}">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Kategori *</label>
                    <select name="category" class="admin-input" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="shirts" @selected(old('category') === 'shirts')>Shirts</option>
                        <option value="tshirts" @selected(old('category') === 'tshirts')>T-Shirts</option>
                        <option value="pants" @selected(old('category') === 'pants')>Pants</option>
                        <option value="outerwear" @selected(old('category') === 'outerwear')>Outerwear</option>
                    </select>
                    @error('category') <span class="admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="admin-form-group">
                    <label>Harga (IDR) *</label>
                    <input type="number" name="price" class="admin-input" value="{{ old('price') }}" min="0" required>
                    @error('price') <span class="admin-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Size</label>
                    <input type="text" name="size" class="admin-input" value="{{ old('size') }}">
                </div>

                <div class="admin-form-group">
                    <label>Stok *</label>
                    <input type="number" name="stock" class="admin-input" value="{{ old('stock') }}" min="0" required>
                    @error('stock') <span class="admin-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="admin-form-group">
                <label>Gambar Produk</label>
                <input type="file" name="image" class="admin-input" accept="image/jpeg,image/png,image/webp">
                <small style="color:#9ca3af; display:block; margin-top:0.35rem;">
                    Format: JPG, PNG, atau WEBP. Maks 2MB.
                </small>
                @error('image') <span class="admin-error">{{ $message }}</span> @enderror
            </div>

            <h3 style="margin-top: 2rem;">Detail Ukuran</h3>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Waist Size</label>
                    <input type="text" name="waist_size" class="admin-input" value="{{ old('waist_size') }}">
                </div>

                <div class="admin-form-group">
                    <label>Length</label>
                    <input type="text" name="length" class="admin-input" value="{{ old('length') }}">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Open Leg</label>
                    <input type="text" name="open_leg" class="admin-input" value="{{ old('open_leg') }}">
                </div>

                <div class="admin-form-group">
                    <label>Chest Width</label>
                    <input type="text" name="chest_width" class="admin-input" value="{{ old('chest_width') }}">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Body Length</label>
                    <input type="text" name="body_length" class="admin-input" value="{{ old('body_length') }}">
                </div>

                <div class="admin-form-group">
                    <label>Sleeve Length</label>
                    <input type="text" name="sleeve_length" class="admin-input" value="{{ old('sleeve_length') }}">
                </div>
            </div>

            <div class="admin-form-group">
                <label>Deskripsi</label>
                <textarea name="description" class="admin-input" rows="4">{{ old('description') }}</textarea>
            </div>

            <div class="admin-form-group">
                <label>
                    <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival') ? 'checked' : '' }}>
                    Tampilkan di "New Arrivals"
                </label>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary">Simpan Produk</button>
        </form>
    </div>
</div>

@endsection