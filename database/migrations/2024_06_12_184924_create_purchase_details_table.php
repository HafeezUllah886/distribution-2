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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchaseID')->constrained('purchases', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->float('pprice', 10);
            $table->float('price', 10);
            $table->float('wsprice', 10);
            $table->float('qty');
            $table->float('tp');
            $table->float('amount');
            $table->float('bonus')->default(0);
            $table->float('gstValue')->default(0);
            $table->date('date');
            $table->foreignId('unitID')->constrained('units', 'id');
            $table->float('unitValue');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
