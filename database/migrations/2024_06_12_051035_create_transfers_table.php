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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from')->constrained('accounts', 'id');
            $table->foreignId('to')->constrained('accounts', 'id');
            $table->foreignId('userID')->constrained('users', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->float('amount');
            $table->date('date');
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
        Schema::dropIfExists('transfers');
    }
};
