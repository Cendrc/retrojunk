<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_code')->unique()->nullable()->after('id');
            $table->string('courier')->nullable()->after('shipping_zone');
            $table->string('tracking_number')->nullable()->after('courier');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_code', 'courier', 'tracking_number', 'confirmed_at', 'shipped_at', 'delivered_at']);
        });
    }
};