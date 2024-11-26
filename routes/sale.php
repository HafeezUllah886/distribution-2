<?php

use App\Http\Controllers\SalePaymentsController;
use App\Http\Controllers\SalesController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

Route::get("sales/getproduct/{id}", [SalesController::class, 'getSignleProduct']);

});
Route::middleware('auth', adminCheck::class)->group(function () {

    Route::resource('sale', SalesController::class);


    Route::get("sales/delete/{id}", [SalesController::class, 'destroy'])->name('sale.delete')->middleware(confirmPassword::class);
    Route::get("sales/gatepass/{id}", [SalesController::class, 'gatePass'])->name('sale.gatePass');
    Route::get("product/searchByCode/{code}", [SalesController::class, 'getProductByCode'])->name('product.searchByCode');

    Route::get('salepayment/{id}', [SalePaymentsController::class, 'index'])->name('salePayment.index');
    Route::get('salepayment/show/{id}', [SalePaymentsController::class, 'show'])->name('salePayment.show');
    Route::get('salepayment/delete/{id}/{ref}', [SalePaymentsController::class, 'destroy'])->name('salePayment.delete')->middleware(confirmPassword::class);
    Route::resource('sale_payment', SalePaymentsController::class);

});
