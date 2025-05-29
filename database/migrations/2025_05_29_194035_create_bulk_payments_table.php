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
        Schema::create('bulk_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userID')->constrained('users', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->string('remarks')->nullable();
            $table->date('date');
            $table->float('amount');
            $table->text('notes')->nullable();
            $table->string('invoiceIDs');
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
        Schema::dropIfExists('bulk_payments');
    }
};
