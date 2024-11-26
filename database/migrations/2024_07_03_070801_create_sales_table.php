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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customerID')->constrained('accounts', 'id');
            $table->foreignId('orderbookerID')->constrained('users', 'id');
            $table->date('date');
            $table->float("wh")->default(0);
            $table->float('whValue')->default(0);
            $table->float('discount')->default(0);
            $table->float('fright')->default(0);
            $table->float('fright1')->default(0);

            $table->float('net')->default(0);
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
        Schema::dropIfExists('sales');
    }
};
