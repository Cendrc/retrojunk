@extends('admin.layout', ['title' => 'Customer'])
@section('content')

<div class="admin-toolbar">
    <form action="{{ route('admin.customers.index') }}" method="GET" class="admin-filters">
        <input type="text" name="search" class="admin-input" 
               placeholder="Cari nama atau email..." value="{{ request('search') }}">
        <button type="submit" class="admin-btn admin-btn--primary">Cari</button>
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        @if($customers->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Total Pesanan</th>
                    <th>Total Belanja</th>
                    <th>Daftar Sejak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="customer-avatar">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                            <strong>{{ $customer->name }}</strong>
                        </div>
                    </td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->order_count }} pesanan</td>
                    <td>IDR {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                    <td>{{ $customer->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="admin-btn admin-btn--small">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="admin-pagination">
            {{ $customers->links() }}
        </div>
        @else
        <p class="admin-empty">Tidak ada customer.</p>
        @endif
    </div>
</div>

@endsection