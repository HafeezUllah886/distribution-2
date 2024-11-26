<?php

use App\Http\Controllers\OrdersController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\CheckOrderOwner;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('orders', OrdersController::class);

    Route::get("order/delete/{id}", [OrdersController::class, 'destroy'])->name('order.delete')->middleware([confirmPassword::class, CheckOrderOwner::class]);
    Route::get("order/sale/{id}", [OrdersController::class, 'sale'])->name('order.sale')->middleware(adminCheck::class);

});
