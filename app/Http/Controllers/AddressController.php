<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    private const MAX_ADDRESSES = 10;

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'address' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $user = auth()->user();

        if ($user->addresses()->count() >= self::MAX_ADDRESSES) {
            return back()->with('error', 'Kamu sudah mencapai batas maksimal ' . self::MAX_ADDRESSES . ' alamat tersimpan. Hapus salah satu alamat lama untuk menambah yang baru.');
        }

        $isFirst = $user->addresses()->count() === 0;

        $address = $user->addresses()->create([
            'label' => $request->label ?: 'Alamat',
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'postal_code' => $request->postal_code,
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'Alamat berhasil disimpan.')->with('new_address_id', $address->id);
    }

    public function destroy($id)
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = auth()->user()->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault($id)
    {
        $user = auth()->user();
        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->findOrFail($id)->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }
}