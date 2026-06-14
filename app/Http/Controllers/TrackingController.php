<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Tampilkan form pencarian tracking
     */
    public function index()
    {
        return view('pages.tracking.index');
    }

    /**
     * Cari order berdasarkan tracking code + email
     */
    public function search(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::where('tracking_code', $request->tracking_code)
            ->where('email', $request->email)
            ->first();

        if (!$order) {
            return back()->withErrors([
                'tracking_code' => 'Kode pesanan atau email tidak ditemukan.'
            ])->withInput();
        }

        return redirect()->route('tracking.show', $order->tracking_code);
    }

    /**
     * Tampilkan detail tracking
     */
    public function show($tracking_code)
    {
        $order = Order::where('tracking_code', $tracking_code)->firstOrFail();
        return view('pages.tracking.show', compact('order'));
    }
}