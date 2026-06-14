<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('email');
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('postal_code');
            $table->json('items');
            $table->decimal('total', 12, 2);
            $table->enum('payment_method', ['qris', 'bank_transfer', 'cod'])->default('bank_transfer');
            $table->string('payment_proof')->nullable();
            $table->enum('status', ['pending', 'awaiting_payment', 'paid', 'confirmed', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};