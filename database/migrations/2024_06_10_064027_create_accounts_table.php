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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->string('title');
            $table->string('title_urdu')->nullable();
            $table->string('type');
            $table->string('category')->nullable();
            $table->string('address')->nullable();
            $table->string('address_urdu')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('c_type')->nullable()->default('Other');
            $table->string('credit_limit')->default(0);
            $table->foreignId('areaID')->constrained('areas', 'id');
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
