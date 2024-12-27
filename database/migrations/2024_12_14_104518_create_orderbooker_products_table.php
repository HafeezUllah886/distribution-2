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
        Schema::create('orderbooker_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderbooker_products');
    }
};
