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
    Route::get('Branch/orders/edit/{id}', [BranchOrdersController::class, 'edit'])->name('Branch.orders.edit');
    Route::post('Branch/orders/update/{id}', [BranchOrdersController::class, 'update'])->name('Branch.orders.update');
   
    Route::get('Branch/orders/show/{id}', [BranchOrdersController::class, 'show'])->name('Branch.orders.show');
    Route::get('Branch/orders/finalize/{id}', [BranchOrdersController::class, 'finalize'])->name('Branch.orders.finalize');

    Route::get("branchorders/getproduct/{id}/{area}", [BranchOrdersController::class, 'getSignleProduct']);

});
