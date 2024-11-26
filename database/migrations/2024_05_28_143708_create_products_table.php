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
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->foreignId('catID')->constrained('categories', 'id');
            $table->foreignId('brandID')->constrained('brands', 'id');
            $table->foreignId('unitID')->constrained('units', 'id');
            $table->float('pprice');
            $table->float('price');
            $table->float('wsprice');
            $table->float('tp')->default(0);
            $table->float('discount')->default(0);
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
