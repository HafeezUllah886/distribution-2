<?php

use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\customerPaymentsReceivingContoller;
use App\Http\Controllers\api\DailyCustomerWisePaymentsReport;
use App\Http\Controllers\api\DailyProductsOrderReport;
use App\Http\Controllers\api\locationTrackingAPIController;
use App\Http\Controllers\api\nonFinanancialInfoController;
use App\Http\Controllers\api\OrdersController;
use App\Http\Controllers\api\SaleApiController;
use App\Http\Controllers\api\OrderbookerBalanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/login', [authController::class, 'login']);

// Sales Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [authController::class, 'logout']);

    Route::post('/orderbookerproducts', [nonFinanancialInfoController::class, 'orderbooker_products']);
    Route::post('/customers', [nonFinanancialInfoController::class, 'customers']);

    Route::post('/order/store', [OrdersController::class, 'store']);
    Route::get('/getorders', [OrdersController::class, 'index']);
    Route::get('/order/delete', [OrdersController::class, 'destroy']);
    Route::post('/order/update', [OrdersController::class, 'update']);

    Route::post('/payment/receiving', [customerPaymentsReceivingContoller::class, 'paymentReceiving']);
    Route::get('/pendinginvoices', [customerPaymentsReceivingContoller::class, 'pendingInvoices']);
    Route::post('/invoicespayment', [customerPaymentsReceivingContoller::class, 'invoicesPayment']);
    Route::get('/lastpayment', [customerPaymentsReceivingContoller::class, 'lastPayment']);

    Route::post('/storelocation', [locationTrackingAPIController::class, 'storeLocation']);

    Route::get('/balance', [OrderbookerBalanceController::class, 'balance']);
    Route::get('/account_statement', [OrderbookerBalanceController::class, 'account_statement']);
    Route::get('/method_wise_balance', [OrderbookerBalanceController::class, 'method_wise_balance']);
    
    Route::get('/product_stock', [OrdersController::class, 'stock']);
    Route::get('/pending_qty', [OrdersController::class, 'pendingQty']);


    Route::get('/daily_customer_wise_payments', [DailyCustomerWisePaymentsReport::class, 'index']);
    Route::get('/daily_products_order_report', [DailyProductsOrderReport::class, 'index']);

});