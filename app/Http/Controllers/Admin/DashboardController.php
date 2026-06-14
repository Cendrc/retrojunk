<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $totalRevenue = Order::whereIn('status', ['confirmed', 'shipped', 'delivered'])->sum('total');
        $totalProducts = Product::count();
        $totalCustomers = User::where('is_admin', false)->count();

        // Recent orders
        $recentOrders = Order::latest()->take(5)->get();

        // Best selling products (mock - dari items di orders)
        $allItems = [];
        foreach (Order::whereIn('status', ['confirmed', 'shipped', 'delivered'])->get() as $order) {
            foreach ($order->items as $item) {
                $name = $item['name'];
                if (!isset($allItems[$name])) {
                    $allItems[$name] = ['name' => $name, 'count' => 0, 'image' => $item['image'] ?? null];
                }
                $allItems[$name]['count']++;
            }
        }
        $bestSelling = collect($allItems)->sortByDesc('count')->take(5);

        return view('admin.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'shippedOrders',
            'totalRevenue',
            'totalProducts',
            'totalCustomers',
            'recentOrders',
            'bestSelling'
        ));
    }
}