<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\brandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TownController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\WarehousesController;
use App\Http\Middleware\adminCheck;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('units', UnitsController::class)->middleware(adminCheck::class);
    Route::resource('categories', CategoriesController::class)->middleware(adminCheck::class);
    Route::resource('brands', brandsController::class)->middleware(adminCheck::class);
    Route::resource('product', ProductsController::class)->middleware(adminCheck::class);
    Route::get('products/index/{category}/{brand}', [ProductsController::class, 'index'])->name('products.index')->middleware(adminCheck::class);
    Route::resource('warehouses', WarehousesController::class);

    Route::resource('towns', TownController::class);
    Route::resource('areas', AreaController::class);

});

