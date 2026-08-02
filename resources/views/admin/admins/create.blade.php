@extends('admin.layout', ['title' => 'Tambah Admin'])
@section('content')

<div class="admin-back">
    <a href="{{ route('admin.admins.index') }}">← Kembali ke Kelola Admin</a>
</div>

<div class="admin-panel">
    <div class="admin-panel__body">
        <form action="{{ route('admin.admins.store') }}" method="POST">
            @csrf

            <div class="admin-form-group">
                <label>Nama *</label>
                <input type="text" name="name" class="admin-input" value="{{ old('name') }}" required>
                @error('name') <span class="admin-error">{{ $message }}</span> @enderror
            </div>

            <div class="admin-form-group">
                <label>Email *</label>
                <input type="email" name="email" class="admin-input" value="{{ old('email') }}" required>
                @error('email') <span class="admin-error">{{ $message }}</span> @enderror
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="admin-input" minlength="8" required>
                    @error('password') <span class="admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="admin-form-group">
                    <label>Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" class="admin-input" minlength="8" required>
                </div>
            </div>

            <button type="submit" class="admin-btn admin-btn--primary">Simpan Admin</button>
        </form>
    </div>
</div>

@endsection
