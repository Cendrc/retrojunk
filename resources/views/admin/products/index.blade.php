@extends('admin.layout', ['title' => 'Produk'])
@section('content')

<div class="admin-toolbar">
    <form action="{{ route('admin.products.index') }}" method="GET" class="admin-filters">
        <input type="text" name="search" class="admin-input" 
               placeholder="Cari nama produk..." value="{{ request('search') }}">
        
        <select name="category" class="admin-input">
            <option value="">Semua Kategori</option>
            <option value="shirts" @selected(request('category') === 'shirts')>Shirts</option>
            <option value="tshirts" @selected(request('category') === 'tshirts')>T-Shirts</option>
            <option value="pants" @selected(request('category') === 'pants')>Pants</option>
            <option value="outerwear" @selected(request('category') === 'outerwear')>Outerwear</option>
        </select>

        <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
    </form>

    <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn--primary">
        + Tambah Produk
    </a>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        @if($products->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Aksi</th>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="admin-btn admin-btn--small">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                              style="display: inline;" onsubmit="return confirm('Yakin mau hapus produk ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Hapus</button>
                        </form>
                    </td>
                    <td>
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                             onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                             style="width: 50px; height: 50px; object-fit: contain; border-radius: 6px;">
                    </td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if($product->code)<br><small>{{ $product->code }}</small>@endif
                    </td>
                    <td>{{ ucfirst($product->category) }}</td>
                    <td>IDR {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        @if($product->is_new_arrival)
                        <span class="admin-badge admin-badge--confirmed">New Arrival</span>
                        @else
                        <span class="admin-badge admin-badge--pending">Regular</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="admin-pagination">
            {{ $products->links() }}
        </div>
        @else
        <p class="admin-empty">Tidak ada produk.</p>
        @endif
    </div>
</div>

@endsection