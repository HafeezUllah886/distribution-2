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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saleID')->constrained('sales', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->float('price', 10);
            $table->float('discount', 10);
            $table->float('discountp', 10);
            $table->float('discountvalue', 10);
            $table->float('qty')->default(0);
            $table->float('loose')->default(0);
            $table->float('pc')->default(0);
            $table->float('bonus')->default(0);
            $table->float('fright', 10);
            $table->float('labor', 10);
            $table->float('claim', 10);
            $table->float('netprice', 10);
            $table->float('amount');
            $table->float('price_amount');
            $table->date('date');
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
        Schema::dropIfExists('sale_details');
    }
};
