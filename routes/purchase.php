<?php

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchasePaymentsController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', adminCheck::class)->group(function () {

    Route::resource('purchase', PurchaseController::class)->middleware(adminCheck::class);

    Route::get("purchases/getproduct/{id}", [PurchaseController::class, 'getSignleProduct']);
    Route::get("purchases/delete/{id}", [PurchaseController::class, 'destroy'])->name('purchases.delete')->middleware(confirmPassword::class);

    Route::get('purchasepayment/{id}', [PurchasePaymentsController::class, 'index'])->name('purchasePayment.index');
    Route::get('purchasepayment/delete/{id}/{ref}', [PurchasePaymentsController::class, 'destroy'])->name('purchasePayment.delete')->middleware(confirmPassword::class);
    Route::resource('purchase_payment', PurchasePaymentsController::class);

});
