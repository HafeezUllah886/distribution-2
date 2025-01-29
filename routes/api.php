<?php

use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\nonFinanancialInfoController;
use App\Http\Controllers\api\OrdersController;
use App\Http\Controllers\Api\SaleApiController;
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
});