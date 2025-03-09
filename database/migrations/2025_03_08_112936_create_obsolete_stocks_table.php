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
        Schema::create('obsolete_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productID')->constrained('products', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->float('qty')->nullable();
            $table->float('loose')->nullable();
            $table->float('pc');
            $table->float('price');
            $table->float('amount');
            $table->foreignId('unitID')->constrained('product_units', 'id');
            $table->float('unitValue');
            $table->string("reason");
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('obsolete_stocks');
    }
};
