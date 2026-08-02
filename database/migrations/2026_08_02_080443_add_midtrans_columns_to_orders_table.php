<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('va_bank')->nullable();
            $table->string('va_number')->nullable();
            $table->text('qr_code_url')->nullable();
            $table->timestamp('midtrans_paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['midtrans_transaction_id', 'midtrans_payment_type', 'va_bank', 'va_number', 'qr_code_url', 'midtrans_paid_at']);
        });
    }
};