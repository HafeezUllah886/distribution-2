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
            $table->foreignId('salesID')->constrained('sales', 'id');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->float('qty');
            $table->float('price', 10);
            $table->float('discount');
            $table->float('ti');
            $table->float('tp');
            $table->float('gst');
            $table->float('gstValue');
            $table->date('date');
            $table->float('bonus')->default(0);
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
        Schema::dropIfExists('sale_details');
    }
};
