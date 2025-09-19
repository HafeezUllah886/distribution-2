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
        Schema::create('customer_advance_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_advanceID')->constrained('customer_advance_payments', 'id');
            $table->foreignId('advance_orderbookerID')->constrained('users', 'id');
            $table->foreignId('consumption_orderbookerID')->constrained('users', 'id');
            $table->foreignId('invoiceID')->constrained('sales', 'id');
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->date('date');
            $table->date('inv_date');
            $table->float('amount');
            $table->bigInteger('refID');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_advance_consumptions');
    }
};
