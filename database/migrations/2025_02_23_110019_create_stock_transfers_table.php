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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('from')->constrained('warehouses', 'id');
            $table->foreignId('to')->constrained('warehouses', 'id');
            $table->foreignId('createdBy')->constrained('users', 'id');
            $table->bigInteger('refID');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stockTransferID')->constrained('stock_transfers');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
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
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_transfer_details');
    }
};
