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
            $table->enum('forwarded', ['Yes', 'No'])->default('No');
            $table->date('forwardedDate')->nullable();
            $table->text('forwardedNotes')->nullable();
            $table->bigInteger('forwardedRefID')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            //  remove the columns
            $table->dropColumn('forwarded');
            $table->dropColumn('forwardedDate');
            $table->dropColumn('forwardedNotes');
            $table->dropColumn('forwardedRefID');
        });
    }
};
