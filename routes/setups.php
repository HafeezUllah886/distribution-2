<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\brandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\productDCController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TownController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\WarehousesController;
use App\Http\Middleware\Admin_BranchAdmin;
use App\Http\Middleware\adminCheck;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('warehouses', WarehousesController::class);
    Route::resource('branches', BranchesController::class);

    Route::resource('towns', TownController::class);
    Route::resource('areas', AreaController::class);

});

Route::middleware(['auth', Admin_BranchAdmin::class])->group(function () {

    Route::resource('units', UnitsController::class);
    Route::resource('categories', CategoriesController::class);
    Route::resource('brands', brandsController::class);
    Route::resource('product', ProductsController::class);
    Route::resource('dc', productDCController::class);
    Route::get('products/index/{category}/{brand}', [ProductsController::class, 'index'])->name('products.index');

});

