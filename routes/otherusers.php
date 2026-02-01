<?php

use App\Http\Controllers\OrderbookerCustomersController;
use App\Http\Controllers\OrderbookerProductsController;
use App\Http\Controllers\OtherusersController;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('otherusers/{type}', [OtherusersController::class, 'index'])->name('otherusers.index');
    Route::post('otherusers/store/{type}', [OtherusersController::class, 'store'])->name('otherusers.store');
    Route::patch('otherusers/update/{id}', [OtherusersController::class, 'update'])->name('otherusers.update');
    Route::get('otherusers/status/{id}', [OtherusersController::class, 'status'])->name('otherusers.status');

    Route::resource('/orderbookerproducts', OrderbookerProductsController::class);
    Route::get('orderbookerproduct/delete/{id}', [OrderbookerProductsController::class, 'destroy'])->name('orderbookerproduct.delete')->middleware([confirmPassword::class]);
    Route::get('orderbookerproduct/show/{id}/{vendor?}', [OrderbookerProductsController::class, 'show'])->name('orderbookerproduct.show');

    Route::resource('/orderbookercustomers', OrderbookerCustomersController::class);
    Route::get('orderbookercustomer/delete/{id}', [OrderbookerCustomersController::class, 'destroy'])->name('orderbookercustomer.delete')->middleware([confirmPassword::class]);
    Route::get('orderbookercustomer/show/{id}/{area?}', [OrderbookerCustomersController::class, 'show'])->name('orderbookercustomer.show');

    Route::get('self/statement', [OtherusersController::class, 'self_statement'])->name('otherusers.self_statement');
    Route::get('user/statement/{id}/{from}/{to}', [OtherusersController::class, 'statement'])->name('otheruser.statement');

    Route::get('/userbalance/{id}', function ($id) {
        $result = getUserAccountBalance($id);

        return response()->json(['data' => $result]);
    });
});
