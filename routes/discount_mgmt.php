<?php

use App\Http\Controllers\DiscountManagementController;
use App\Http\Controllers\ReturnsController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

Route::resource('discount', DiscountManagementController::class);
Route::get('discount/delete/{ref}', [DiscountManagementController::class, 'destroy'])->name('discount.delete')->middleware(confirmPassword::class);

});
