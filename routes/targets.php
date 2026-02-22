<?php

use App\Http\Controllers\BalanceTargetsController;
use App\Http\Controllers\TargetsController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('targets', TargetsController::class);
    Route::get('target/delete/{id}', [TargetsController::class, 'destroy'])->name('target.delete')->middleware(confirmPassword::class);

    Route::resource('balance_targets', BalanceTargetsController::class);
    Route::get('balance_target/delete/{id}', [BalanceTargetsController::class, 'destroy'])->name('balance_target.delete')->middleware(confirmPassword::class);

});
