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
        Schema::create('staff_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fromID')->constrained('users');
            $table->foreignId('receivedBy')->constrained('users');
            $table->float('amount');
            $table->date('date');
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->text('notes')->nullable();
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_payments');
    }
};
