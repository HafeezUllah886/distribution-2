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

        Schema::create('towns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('townID')->constrained('towns', 'id');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->string('name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('town');
        Schema::dropIfExists('areas');
    }
};
