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
        Schema::create('order_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->string('orderID');
            $table->string('customer');
            $table->string('orderbooker');
            $table->string('product');
            $table->string('unit');
            $table->string('qty');
            $table->string('loose');
            $table->string('date');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reminders');
    }
};
