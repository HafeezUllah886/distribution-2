<?php

use App\Http\Controllers\SalePaymentsController;
use App\Http\Controllers\SalesController;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get("sales/getproduct/{id}/{warehouse}/{area}/{customer}", [SalesController::class, 'getSignleProduct']);

    Route::resource('sale', SalesController::class);
    Route::get("sale/{id}/urdu", [SalesController::class, 'showUrdu'])->name('sale.showUrdu');

    Route::get("sales/delete/{id}", [SalesController::class, 'destroy'])->name('sale.delete')->middleware(confirmPassword::class);
    Route::get("sales/gatepass/{id}", [SalesController::class, 'gatePass'])->name('sale.gatePass');
    Route::get("product/searchByCode/{code}", [SalesController::class, 'getProductByCode'])->name('product.searchByCode');

    Route::get('salepayment/{id}', [SalePaymentsController::class, 'index'])->name('salePayment.index');
    Route::get('salepayment/show/{id}', [SalePaymentsController::class, 'show'])->name('salePayment.show');
    Route::get('salepayment/delete/{id}/{ref}', [SalePaymentsController::class, 'destroy'])->name('salePayment.delete')->middleware(confirmPassword::class);
    Route::resource('sale_payment', SalePaymentsController::class);

    Route::get('sales/minor_edit', [SalesController::class, 'minor_edit'])->name('sale.minor_edit');

    Route::get('orderbooker/getcustomers/{id}', [SalesController::class, 'orderbooker_customers'])->name('sale.getCustomers');
});
