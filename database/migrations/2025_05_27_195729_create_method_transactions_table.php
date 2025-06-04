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
        Schema::create('method_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->foreignId('userID')->constrained('users', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->float('cr')->default(0);
            $table->float('db')->default(0);
            $table->date('date');
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
        Schema::dropIfExists('method_transactions');
    }
};
