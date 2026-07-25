<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Split kolom 'status' jadi 'order_status' dan 'payment_status'.
     * 
     * order_status: pending, confirmed, shipped, delivered, cancelled
     *   → menangani alur pesanan (dari dibuat sampai sampai ke customer)
     * 
     * payment_status: unpaid, awaiting_verification, paid, rejected
     *   → menangani alur pembayaran
     */
    public function up(): void
    {
        // 1. Tambah kolom order_status (temporary nullable, akan diisi dari data lama)
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('order_status', [
                'pending', 'confirmed', 'shipped', 'delivered', 'cancelled'
            ])->default('pending')->after('status');
            
            $table->enum('payment_status', [
                'unpaid', 'awaiting_verification', 'paid', 'rejected'
            ])->default('unpaid')->after('order_status');
        });

        // 2. Migrasi data lama: mapping status → (order_status, payment_status)
        // Ambil semua order yang ada
        $orders = DB::table('orders')->get();
        
        foreach ($orders as $order) {
            $orderStatus = 'pending';
            $paymentStatus = 'unpaid';
            
            // Mapping dari status lama ke pasangan (order_status, payment_status)
            switch ($order->status) {
                case 'pending':
                    $orderStatus = 'pending';
                    $paymentStatus = $order->payment_proof ? 'awaiting_verification' : 'unpaid';
                    break;
                    
                case 'confirmed':
                    $orderStatus = 'confirmed';
                    $paymentStatus = 'paid';
                    break;
                    
                case 'shipped':
                    $orderStatus = 'shipped';
                    $paymentStatus = 'paid';
                    break;
                    
                case 'delivered':
                    $orderStatus = 'delivered';
                    $paymentStatus = 'paid';
                    break;
                    
                case 'cancelled':
                    $orderStatus = 'cancelled';
                    $paymentStatus = 'unpaid';
                    break;
            }
            
            // Update order dengan status baru
            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'order_status' => $orderStatus,
                    'payment_status' => $paymentStatus,
                ]);
        }

        // 3. Drop kolom status yang lama
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Rollback: gabungkan lagi jadi kolom 'status' tunggal.
     */
    public function down(): void
    {
        // 1. Tambah kolom status kembali
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 'confirmed', 'shipped', 'delivered', 'cancelled'
            ])->default('pending')->after('payment_method');
        });

        // 2. Migrasi data balik: pakai order_status sebagai status utama
        DB::statement("UPDATE orders SET status = order_status");

        // 3. Drop kolom order_status dan payment_status
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_status', 'payment_status']);
        });
    }
};