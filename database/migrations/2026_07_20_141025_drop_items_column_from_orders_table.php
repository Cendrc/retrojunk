<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop kolom 'items' dari tabel orders.
     * Data item pesanan sekarang disimpan di tabel order_items.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('items');
        });
    }

    /**
     * Rollback: Tambahkan kembali kolom 'items' bertipe JSON.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('items')->nullable()->after('postal_code');
        });
    }
};