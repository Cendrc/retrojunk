@extends('admin.layout', ['title' => 'Tambah Produk'])
@section('content')

<div class="admin-back">
    <a href="{{ route('admin.products.index') }}">← Kembali ke Daftar Produk</a>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Nama Produk *</label>
                    <input type="text" name="name" class="admin-input" value="{{ old('name') }}" required>
                    @error('name')<small class="admin-error">{{ $message }}</small>@enderror
                </div>

                <div class="admin-form-group">
                    <label>Kode Produk</label>
                    <input type="text" name="code" class="admin-input" value="{{ old('code') }}" placeholder="Contoh: CG-15">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Kategori *</label>
                    <select name="category" class="admin-input" required>
                        <option value="">- Pilih Kategori -</option>
                        <option value="shirts" @selected(old('category') === 'shirts')>Shirts</option>
                        <option value="tshirts" @selected(old('category') === 'tshirts')>T-Shirts</option>
                        <option value="pants" @selected(old('category') === 'pants')>Pants</option>
                        <option value="outerwear" @selected(old('category') === 'outerwear')>Outerwear</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label>Harga (IDR) *</label>
                    <input type="number" name="price" class="admin-input" value="{{ old('price') }}" min="0" required>
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Size</label>
                    <input type="text" name="size" class="admin-input" value="{{ old('size') }}" placeholder="Contoh: M-L">
                </div>

                <div class="admin-form-group">
                    <label>Stok *</label>
                    <input type="number" name="stock" class="admin-input" value="{{ old('stock', 1) }}" min="0" required>
                </div>
            </div>

            <div class="admin-form-group">
                <label>Path Gambar</label>
                <input type="text" name="image" class="admin-input" value="{{ old('image') }}" 
                       placeholder="Contoh: /images/nama-produk.png">
                <small style="color: #888;">Upload file ke folder public/images/ lalu masukkan path-nya</small>
            </div>

            <h3 style="margin-top: 2rem;">Detail Ukuran (Optional)</h3>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Waist Size (Celana)</label>
                    <input type="text" name="waist_size" class="admin-input" value="{{ old('waist_size') }}" placeholder="74 cm">
                </div>

                <div class="admin-form-group">
                    <label>Length</label>
                    <input type="text" name="length" class="admin-input" value="{{ old('length') }}" placeholder="98 cm">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Open Leg</label>
                    <input type="text" name="open_leg" class="admin-input" value="{{ old('open_leg') }}" placeholder="22 cm">
                </div>

                <div class="admin-form-group">
                    <label>Chest Width (Outerwear)</label>
                    <input type="text" name="chest_width" class="admin-input" value="{{ old('chest_width') }}" placeholder="58 cm">
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Body Length</label>
                    <input type="text" name="body_length" class="admin-input" value="{{ old('body_length') }}" placeholder="60 cm">
                </div>

                <div class="admin-form-group">
                    <label>Sleeve Length</label>
                    <input type="text" name="sleeve_length" class="admin-input" value="{{ old('sleeve_length') }}" placeholder="55 cm">
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

            <button type="submit" class="admin-btn admin-btn--primary">Tambah Produk</button>
        </form>
    </div>
</div>

@endsection