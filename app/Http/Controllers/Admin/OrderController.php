<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
        $order = Order::findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'courier' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Update timestamps berdasarkan status
        if ($newStatus === 'confirmed' && !$order->confirmed_at) {
            $order->confirmed_at = now();
        }
        if ($newStatus === 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }
        if ($newStatus === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->status = $newStatus;
        $order->courier = $request->courier;
        $order->tracking_number = $request->tracking_number;
        $order->notes = $request->notes;
        $order->save();

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Status pesanan berhasil diupdate dari "' . $oldStatus . '" menjadi "' . $newStatus . '"');
    }
}