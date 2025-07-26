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
        Schema::create('payments_receiving', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depositerID')->constrained('accounts', 'id');
            $table->foreignId('userID')->constrained('users', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->float('amount');
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
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
        Schema::dropIfExists('payments_receiving');
    }
};
