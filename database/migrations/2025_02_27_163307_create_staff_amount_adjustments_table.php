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
        Schema::create('staff_amount_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffID')->constrained('users');
            $table->foreignId('userID')->constrained('users');
            $table->foreignId('branchID')->constrained('branches');
            $table->float('amount');
            $table->enum('type', ['debit', 'credit']);
            $table->date('date');
            $table->string('notes')->nullable();
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_amount_adjustments');
    }
};
