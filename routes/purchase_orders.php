<?php

use App\Http\Controllers\BranchOrdersController;
use App\Http\Controllers\OrderDeliveryController;
use App\Http\Controllers\OrderReminderController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderDeliveryController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\BranchAdmin_OperatorCheck;
use App\Http\Middleware\CheckOrderOwner;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', BranchAdmin_OperatorCheck::class])->group(function () {

    Route::resource('purchase_order', PurchaseOrderController::class);
    Route::get('purchase_order_receving/create/{id}', [PurchaseOrderDeliveryController::class, 'create'])->name('purchaseOrderReceiveing');

    Route::resource('purchase_order_receiving', PurchaseOrderDeliveryController::class);

  
});
