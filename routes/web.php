<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\brandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\WarehousesController;
use App\Http\Middleware\adminCheck;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/auth.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/purchase.php';
require __DIR__ . '/stock.php';
require __DIR__ . '/sale.php';
require __DIR__ . '/reports.php';
require __DIR__ . '/orders.php';
require __DIR__ . '/targets.php';
require __DIR__ . '/otherusers.php';
require __DIR__ . '/setups.php';

Route::middleware('auth')->group(function () {

    Route::get('/', [dashboardController::class, 'index'])->name('dashboard');


});


