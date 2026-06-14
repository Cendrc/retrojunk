<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing data: paid/awaiting_payment → pending (kalau belum dikonfirmasi)
        DB::statement("UPDATE orders SET status = 'pending' WHERE status IN ('awaiting_payment', 'paid')");
        
        // Change enum
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('pending', 'awaiting_payment', 'paid', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};