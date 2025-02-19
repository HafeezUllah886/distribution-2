<?php

use App\Http\Controllers\BranchOrdersController;
use App\Http\Controllers\OrdersController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\BranchAdmin_OperatorCheck;
use App\Http\Middleware\CheckOrderOwner;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', BranchAdmin_OperatorCheck::class])->group(function () {

    Route::get('Branch/orders', [BranchOrdersController::class, 'index'])->name('Branch.orders');

});
