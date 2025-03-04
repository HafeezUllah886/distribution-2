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
        Schema::create('returns_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('returnID')->constrained('returns', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->float('price', 10);
            $table->float('qty')->default(0);
            $table->float('loose')->default(0);
            $table->float('pc')->default(0);
            $table->float('amount');
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
        Schema::dropIfExists('returns_details');
    }
};
