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
        // Migration cache table bawaan project ini masih pakai stub lama
        // (key/value/expiration), padahal Illuminate\Cache\DatabaseLock di
        // laravel/framework ^11.0 yang terpasang butuh kolom "owner" untuk
        // menandai siapa pemegang lock saat ini. Tanpa ini, apa pun yang
        // pakai Cache::lock() (termasuk penguncian bawaan scheduler supaya
        // task tidak dobel jalan) akan gagal dengan error kolom tidak ada.
        Schema::table('cache_locks', function (Blueprint $table) {
            if (Schema::hasColumn('cache_locks', 'value')) {
                $table->dropColumn('value');
            }
            if (!Schema::hasColumn('cache_locks', 'owner')) {
                $table->string('owner')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cache_locks', function (Blueprint $table) {
            if (Schema::hasColumn('cache_locks', 'owner')) {
                $table->dropColumn('owner');
            }
            if (!Schema::hasColumn('cache_locks', 'value')) {
                $table->mediumText('value')->nullable();
            }
        });
    }
};
