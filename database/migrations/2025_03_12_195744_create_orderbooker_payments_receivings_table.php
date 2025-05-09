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
        Schema::create('orderbooker_payments_receivings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->foreignId('receivedBy')->constrained('users', 'id');
            $table->float('amount');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->bigInteger('refID');
            $table->string('payment_method');
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderbooker_payments_receivings');
    }
};
