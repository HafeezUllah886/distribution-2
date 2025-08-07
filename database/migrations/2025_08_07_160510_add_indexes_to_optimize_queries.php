
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToOptimizeQueries extends Migration
{
    public function up()
    {
        // sale_details table
        Schema::table('sale_details', function (Blueprint $table) {
            $table->index('date');
            $table->index('productID');
        });

        // products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('branchID');
            $table->index('vendorID');
        });
    }

    public function down()
    {
        // Reverse the indexes if needed
        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['productID']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['branchID']);
            $table->dropIndex(['vendorID']);
        });
    }
}
