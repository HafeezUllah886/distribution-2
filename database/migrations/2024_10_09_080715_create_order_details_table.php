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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orderID')->constrained('orders', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->float('qty');
            $table->float('price', 10);
            $table->float('discount')->default(0);
            $table->float('amount');
            $table->date('date');
            $table->foreignId('unitID')->constrained('units', 'id');
            $table->float('unitValue');
            $table->float('bonus')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
