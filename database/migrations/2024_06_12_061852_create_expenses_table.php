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

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userID')->constrained('users', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('categoryID')->constrained('expense_categories', 'id');
            $table->date('date');
            $table->float('amount');
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->text('notes');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};
