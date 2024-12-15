<?php

use App\Http\Controllers\OrderbookerProductsController;
use App\Http\Controllers\OtherusersController;
use App\Http\Controllers\UserAccountsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('otherusers/{type}', [OtherusersController::class, 'index'])->name('otherusers.index');
    Route::post('otherusers/store/{type}', [OtherusersController::class, 'store'])->name('otherusers.store');
    Route::patch('otherusers/update/{id}', [OtherusersController::class, 'update'])->name('otherusers.update');
    Route::get('otherusers/status/{id}', [OtherusersController::class, 'status'])->name('otherusers.status');

    Route::resource('/userAccounts', UserAccountsController::class);
    Route::resource('/orderbooker/products', OrderbookerProductsController::class);
});
