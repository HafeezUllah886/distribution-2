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
        Schema::table('obsolete_stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('createdBy')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obsolete_stock', function (Blueprint $table) {
            $table->dropColumn('createdBy');
        });
    }
};
