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
        Schema::create('generate_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employeeID')->constrained('employees', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->float('salary');
            $table->date('date');
            $table->date('month');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generate_salaries');
    }
};
