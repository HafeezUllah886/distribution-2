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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendorID')->constrained('accounts', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->string("inv")->nullable();
            $table->string("bilty")->nullable();
            $table->string("transporter")->nullable();
            $table->float('net')->default(0);
            $table->string('status')->default('Pending');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
