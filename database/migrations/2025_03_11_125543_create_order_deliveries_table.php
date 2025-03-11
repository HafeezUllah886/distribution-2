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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orderID')->constrained('orders', 'id')->cascadeOnDelete();
            $table->foreignId('salesID')->constrained('sales', 'id')->cascadeOnDelete();
            $table->foreignId('productID')->constrained('products', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->float('qty')->default(0);
            $table->float('loose')->default(0);
            $table->float('pc')->default(0);
            $table->foreignId('unitID')->constrained('product_units', 'id');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
