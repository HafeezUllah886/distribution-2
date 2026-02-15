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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->date('startDate');
            $table->date('endDate');
            $table->time('notificationStartTime');
            $table->time('notificationEndTime');
            $table->foreignId('productID')->constrained('products', 'id');
            $table->float('pc');
            $table->foreignId('unitID')->constrained('units', 'id');
            $table->float('unit_value');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
