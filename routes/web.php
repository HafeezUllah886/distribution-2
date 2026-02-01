<?php

use App\Http\Controllers\adminDashboardController;
use App\Http\Controllers\dashboardController;
use App\Models\accounts;
use App\Models\orderbooker_products;
use App\Models\product_units;
use App\Models\products;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';
require __DIR__.'/finance.php';
require __DIR__.'/purchase.php';
require __DIR__.'/stock.php';
require __DIR__.'/sale.php';
require __DIR__.'/return.php';
require __DIR__.'/reports.php';
require __DIR__.'/orders.php';
require __DIR__.'/purchase_orders.php';
require __DIR__.'/targets.php';
require __DIR__.'/otherusers.php';
require __DIR__.'/setups.php';
require __DIR__.'/employees.php';
require __DIR__.'/discount_mgmt.php';

Route::middleware('auth')->group(function () {
    Route::get('/', [dashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/{branch?}/{from?}/{to?}', [adminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('accounts_by_type/{type}', function ($type) {
        $accounts = accounts::where('type', $type)->currentBranch()->select('id as value', 'title as text')->get();

        return response()->json($accounts);
    })->name('accounts_by_type');

    Route::get('customer_by_area/{areaID}', function ($areaID) {
        $customers = accounts::customer()->where('areaID', $areaID)->currentBranch()->select('id as value', 'title as text')->get();

        return response()->json($customers);
    })->name('customer_by_area');

    Route::get('getSignleProduct/{id}', function ($id) {
        $product = products::with('units')->find($id);

        return $product;
    })->name('getSignleProduct');

    Route::get('getOrderbookerProducts/{orderbookerID}', function ($orderbookerID) {
        $products = orderbooker_products::where('orderbookerID', $orderbookerID)->with('product')->get();
        $products_array = [];
        foreach ($products as $product) {
            $value = $product->product->id;
            $text = $product->product->name;
            $products_array[] = compact('value', 'text');
        }

        return response()->json($products_array);
    })->name('getOrderbookerProducts');

    Route::get('getUnits/{productID}', function ($productID) {
        $units = product_units::where('productID', $productID)->get();
        $units_array = [];
        foreach ($units as $unit) {
            $value = $unit->id;
            $text = $unit->unit_name;
            $unit_value = $unit->value;
            $units_array[] = compact('value', 'text', 'unit_value');
        }

        return response()->json($units_array);
    })->name('getUnits');
});
