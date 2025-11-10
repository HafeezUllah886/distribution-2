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
        Schema::create('discount_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('customerID')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('productID')->constrained('products')->cascadeOnDelete();
            $table->float('discount')->default(0);
            $table->float('discountp')->default(0);
            $table->date('start_date')->default(now());
            $table->date('end_date')->default(now());
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_management');
    }
};
