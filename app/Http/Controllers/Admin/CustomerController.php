<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_admin', false);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        foreach ($customers as $customer) {
            $customer->order_count = Order::where('email', $customer->email)->count();
            $customer->total_spent = Order::where('email', $customer->email)
                ->whereIn('status', ['confirmed', 'shipped', 'delivered'])
                ->sum('total');
        }

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::findOrFail($id);
        $orders = Order::where('email', $customer->email)->latest()->get();
        
        return view('admin.customers.show', compact('customer', 'orders'));
    }
}