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
            $table->foreignId('unitID')->constrained('product_units', 'id');
            $table->float('pack_qty')->default(0);
            $table->float('loose_qty')->default(0);
            $table->float('total_pieces')->default(0);
            $table->float('price', 10);
            $table->date('date');
            $table->bigInteger('refID');
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
