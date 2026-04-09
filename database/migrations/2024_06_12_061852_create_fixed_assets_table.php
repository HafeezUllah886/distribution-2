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

        Schema::create('fixed_assets_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->timestamps();
        });

        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branchID')->constrained('branches', 'id');
            $table->foreignId('categoryID')->constrained('fixed_assets_categories', 'id');
            $table->string('item_description');
            $table->date('date');
            $table->float('amount');
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->string('purchase_status')->nullable();
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->text('notes');
            $table->bigInteger('refID');
            $table->timestamps();
        });

        Schema::create('fixed_assets_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixedAssetID')->constrained('fixed_assets', 'id');
            $table->date('date');
            $table->float('amount');
            $table->enum('method', ['Cash', 'Online', 'Cheque', 'Other']);
            $table->string('number')->nullable();
            $table->string('bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->text('notes');
            $table->bigInteger('refID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_assets_categories');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('fixed_assets_sales');
    }
};
