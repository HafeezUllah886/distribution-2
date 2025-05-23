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
        Schema::create('product_dcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productID')->constrained('products', 'id');
            $table->foreignId('areaID')->constrained('areas', 'id');
            $table->float('dc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_dcs');
    }
};
