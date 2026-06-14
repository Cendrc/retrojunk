<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2);
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('size')->nullable();
            // Field untuk pants/celana
            $table->string('waist_size')->nullable();
            $table->string('length')->nullable();
            $table->string('open_leg')->nullable();
            // Field untuk jacket/outerwear
            $table->string('chest_width')->nullable();
            $table->string('body_length')->nullable();
            $table->string('sleeve_length')->nullable();
            // Lainnya
            $table->string('code')->nullable();
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->integer('stock')->default(1);
            $table->boolean('is_new_arrival')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};