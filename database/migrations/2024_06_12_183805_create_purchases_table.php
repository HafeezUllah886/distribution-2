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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendorID')->constrained('accounts', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->date('orderdate');
            $table->date('recdate');
            $table->string("inv")->nullable();
            $table->string("bilty")->nullable();
            $table->string("transporter")->nullable();
            $table->text('notes')->nullable();
            $table->float('net')->default(0);
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
