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
        Schema::create('balance_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->date('startDate');
            $table->date('endDate');
            $table->float('start_value');
            $table->float('target_value');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_targets');
    }
};
