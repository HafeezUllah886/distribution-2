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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->foreignId('supplymanID')->constrained('accounts', 'id');
            $table->date('orderdate');
            $table->date('date');
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
        Schema::dropIfExists('sales');
    }
};
