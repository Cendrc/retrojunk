<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::where('is_admin', true)->latest()->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Akun admin baru berhasil dibuat.');
    }

    public function destroy($id)
    {
        if ((int) $id === auth()->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Anda tidak bisa mencabut akses admin milik akun sendiri.');
        }

        if (User::where('is_admin', true)->count() <= 1) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Tidak bisa mencabut akses admin terakhir yang tersisa.');
        }

        $admin = User::where('is_admin', true)->findOrFail($id);
        $admin->update(['is_admin' => false]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Akses admin berhasil dicabut.');
    }
}
