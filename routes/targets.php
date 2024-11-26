<?php

use App\Http\Controllers\TargetsController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', adminCheck::class)->group(function () {

    Route::resource('targets', TargetsController::class);
    Route::get('target/delete/{id}', [TargetsController::class, 'destroy'])->name('target.delete')->middleware(confirmPassword::class);

});

