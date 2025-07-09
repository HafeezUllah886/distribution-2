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
        Schema::create('transactions_ques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userID')->constrained('users');
            $table->foreignId('customerID')->constrained('accounts');
            $table->foreignId('orderbookerID')->constrained('users');
            $table->foreignId('branchID')->constrained('branches');
            $table->string('method');
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->float('amount');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->bigInteger('refID');
            $table->bigInteger('trefID')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions_ques');
    }
};
