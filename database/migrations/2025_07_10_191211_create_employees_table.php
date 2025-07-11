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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->string('fname');
            $table->string('designation');
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->float('salary');
            $table->float('limit');
            $table->date('doe');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
