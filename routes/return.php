<?php

use App\Http\Controllers\ReturnsController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

Route::get("return/getproduct/{id}", [ReturnsController::class, 'getSignleProduct']);

});
Route::middleware('auth')->group(function () {

    Route::resource('return', ReturnsController::class);
    Route::get("return/delete/{id}", [ReturnsController::class, 'destroy'])->name('return.delete')->middleware(confirmPassword::class);
});
