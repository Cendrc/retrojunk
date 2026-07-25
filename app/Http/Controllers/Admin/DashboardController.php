<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        // STATS
        $totalOrders = Order::count();
        
        // Pending orders (order_status = pending)
        $pendingOrders = Order::where('order_status', 'pending')->count();
        
        // Shipped orders (order_status = shipped)
        $shippedOrders = Order::where('order_status', 'shipped')->count();
        
        // Revenue: total dari order yang sudah paid (payment_status = paid)
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        
        $totalProducts = Product::count();
        $totalCustomers = User::where('is_admin', false)->count();

        // RECENT ORDERS
        $recentOrders = Order::with('items.product')
            ->latest()
            ->take(5)
            ->get();

        // BEST SELLING PRODUCTS
        $bestSelling = OrderItem::select(
                'product_name as name',
                'product_image as image',
                DB::raw('SUM(quantity) as count')
            )
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->groupBy('product_name', 'product_image')
            ->orderByDesc('count')
            ->take(5)
            ->get();

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