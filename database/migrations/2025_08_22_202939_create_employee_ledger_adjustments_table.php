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
        Schema::create('employee_ledger_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employeeID')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branchID')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('userID')->constrained('users');
            $table->decimal('amount', 15, 2);
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
        Schema::dropIfExists('employee_ledger_adjustments');
    }
};
