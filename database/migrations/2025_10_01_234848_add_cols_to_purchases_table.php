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
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('driver_name')->nullable();
            $table->string('driver_contact')->nullable();
            $table->string('cno')->nullable();
            $table->foreignId('freightID')->nullable()->constrained('accounts', 'id');
            $table->foreignId('expenseCategoryID')->nullable()->constrained('expense_categories', 'id');
            $table->string('freight_status')->default('Unpaid');
            $table->string('freight_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['freightID']);
            $table->dropForeign(['expenseCategoryID']);
            $table->dropColumn(['freightID', 'expenseCategoryID']);
            $table->dropColumn(['driver_name', 'driver_contact', 'cno', 'freight_status', 'freight_notes']);
        });
    }
};
