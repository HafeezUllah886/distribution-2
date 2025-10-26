<?php

use App\Http\Controllers\adminDashboardController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\brandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\WarehousesController;
use App\Http\Middleware\adminCheck;
use App\Models\accounts;
use App\Models\products;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/auth.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/purchase.php';
require __DIR__ . '/stock.php';
require __DIR__ . '/sale.php';
require __DIR__ . '/return.php';
require __DIR__ . '/reports.php';
require __DIR__ . '/orders.php';
require __DIR__ . '/purchase_orders.php';
require __DIR__ . '/targets.php';
require __DIR__ . '/otherusers.php';
require __DIR__ . '/setups.php';
require __DIR__ . '/employees.php';

Route::middleware('auth')->group(function () {
    Route::get('/', [dashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/{branch?}/{from?}/{to?}', [adminDashboardController::class, 'index'])->name('admin.dashboard');


    Route::get('accounts_by_type/{type}', function ($type) {
        $accounts = accounts::where('type', $type)->currentBranch()->select('id as value', 'title as text')->get();

        return response()->json($accounts);
    })->name('accounts_by_type');
    

     Route::get('getSignleProduct/{id}', function ($id) {
        $product = products::with('units')->find($id);

        return $product;
    })->name('getSignleProduct');

});


