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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('nameurdu');
            $table->foreignId('vendorID')->constrained('accounts', 'id');
            $table->foreignId('catID')->constrained('categories', 'id');
            $table->foreignId('brandID')->constrained('brands', 'id');
            $table->float('pprice')->default(0);
            $table->float('price')->default(0);
            $table->float('discount')->default(0);
            $table->float('fright')->default(0);
            $table->float('labor')->default(0);
            $table->float('claim')->default(0);
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
