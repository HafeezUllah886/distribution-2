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
        Schema::create('users_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('userID')->constrained('users', 'id');
            $table->date('date');
            $table->float('cr', 2)->default(0);
            $table->float('db', 2)->default(0);
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
        Schema::dropIfExists('users_transactions');
    }
};
