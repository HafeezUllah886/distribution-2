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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('warehouseID')->constrained('warehouses', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->json('invoices')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('returns');
    }
};
