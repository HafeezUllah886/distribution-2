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
        Schema::table('cheques', function (Blueprint $table) {
            $table->foreignId('forwardedTo')
          ->nullable() // Make it nullable first
          ->constrained('accounts', 'id')
          ->nullOnDelete(); // Optional: set NULL when account is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            $table->dropForeign(['forwardedTo']);
            $table->dropColumn('forwardedTo');
        });
    }
};
