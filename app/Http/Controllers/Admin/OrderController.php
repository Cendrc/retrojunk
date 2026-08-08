<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.product');

        // Filter berdasarkan order_status (alur pesanan)
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Filter berdasarkan payment_status (alur pembayaran)
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search berdasarkan tracking code, email, atau nama
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('tracking_code', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'order_status'    => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'payment_status'  => 'required|in:unpaid,awaiting_verification,paid,rejected',
            'courier'         => 'nullable|string|required_if:order_status,shipped',
            'tracking_number' => 'nullable|string|required_if:order_status,shipped',
            'notes'           => 'nullable|string',
        ], [
            'courier.required_if' => 'Kurir wajib diisi saat status pesanan diubah menjadi "Shipped".',
            'tracking_number.required_if' => 'Nomor resi wajib diisi saat status pesanan diubah menjadi "Shipped".',
        ]);

        $order = Order::with('items.product')->findOrFail($id);
        $oldOrderStatus = $order->order_status;
        $newOrderStatus = $request->order_status;

        // Update timestamps berdasarkan perubahan order_status
        // Timestamps hanya di-set pertama kali status berubah (tidak overwrite)
        if ($newOrderStatus === 'confirmed' && !$order->confirmed_at) {
            $order->confirmed_at = now();
        }
        if ($newOrderStatus === 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }
        if ($newOrderStatus === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        // Update kedua status
        $order->order_status = $newOrderStatus;
        $order->payment_status = $request->payment_status;
        $order->courier = $request->courier;
        $order->tracking_number = $request->tracking_number;
        $order->notes = $request->notes;
        $order->save();

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Status pesanan berhasil diupdate. Order: "' . $oldOrderStatus . '" → "' . $newOrderStatus . '"');
    }
}