<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Berat default per kategori (gram) — dipakai buat produk lama yang
     * belum pernah diisi beratnya, supaya kalkulasi ongkir tetap jalan.
     */
    private const DEFAULT_WEIGHTS = [
        'pants' => 400,
        'outerwear' => 500,
        'shirts' => 200,
        'tshirts' => 180,
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('products', 'weight')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('weight')->default(300)->after('stock');
            });
        }

        foreach (self::DEFAULT_WEIGHTS as $category => $grams) {
            DB::table('products')->where('category', $category)->update(['weight' => $grams]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'weight')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('weight');
            });
        }
    }
};
