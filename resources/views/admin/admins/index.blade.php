@extends('admin.layout', ['title' => 'Kelola Admin'])
@section('content')

<div class="admin-toolbar">
    <a href="{{ route('admin.admins.create') }}" class="admin-btn admin-btn--primary">
        + Tambah Admin
    </a>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        @if($admins->count() > 0)
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Terdaftar Sejak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td><strong>{{ $admin->name }}</strong></td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->created_at->format('d M Y') }}</td>
                    <td>
                        @if($admin->id === auth()->id())
                        <span class="admin-badge admin-badge--confirmed">Akun Anda</span>
                        @else
                        <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST"
                              style="display: inline;" onsubmit="return confirm('Cabut akses admin untuk {{ $admin->email }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">Cabut Akses</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="admin-empty">Tidak ada admin.</p>
        @endif
    </div>
</div>

@endsection
