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
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('has_expense')->default(false);
            $table->decimal('expense_amount', 15, 2)->default(0)->after('has_expense');
            $table->boolean('show_expense')->default(false)->after('expense_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('has_expense');
            $table->dropColumn('expense_amount');
            $table->dropColumn('show_expense');
        });
    }
};
