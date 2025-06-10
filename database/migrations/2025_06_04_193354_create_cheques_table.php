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
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerID')->constrained('accounts', 'id')->cascadeOnDelete();
            $table->foreignId('userID')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('branchID')->constrained('branches', 'id')->cascadeOnDelete();
            $table->date('cheque_date');
            $table->float('amount');
            $table->string('number');
            $table->string('bank');
            $table->enum('status', ['pending', 'cleared', 'bounced'])->default('pending');
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
        Schema::dropIfExists('cheques');
    }
};
