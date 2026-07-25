<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migration untuk membuat tabel order_items.
     * Tabel ini menghubungkan pesanan dengan produk secara relasional.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke orders (kalau order dihapus, order_items ikut terhapus)
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');
            
            // Foreign key ke products (kalau product dihapus, order_item TIDAK terhapus)
            // Karena kita butuh data historis untuk pesanan lama
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('restrict');
            
            // Snapshot data produk saat dipesan
            // Kalau product berubah nama/harga di masa depan, order tetap punya info asli
            $table->string('product_name');
            $table->decimal('product_price', 10, 2);
            $table->string('product_image')->nullable();
            
            // Quantity & subtotal
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 10, 2);
            
            $table->timestamps();
            
            // Index untuk query yang sering dilakukan
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Rollback migration - hapus tabel order_items.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};