<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'subdistrict')) {
                $table->string('subdistrict')->nullable()->after('district');
            }
            if (!Schema::hasColumn('orders', 'shipping_service')) {
                $table->string('shipping_service')->nullable()->after('courier');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subdistrict', 'shipping_service']);
        });
    }
};
