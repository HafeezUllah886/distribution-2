<?php

use App\Http\Controllers\DeleteRequestsController;
use App\Http\Middleware\Admin_BranchAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', Admin_BranchAdmin::class])->group(function () {
    Route::get('delete_request', [DeleteRequestsController::class, 'index'])->name('delete_request.index');
    Route::get('delete_request/approve/{id}', [DeleteRequestsController::class, 'approve'])->name('delete_request.approve');
    Route::get('delete_request/reject/{id}', [DeleteRequestsController::class, 'reject'])->name('delete_request.reject');
    Route::get('delete_request/delete/{id}', [DeleteRequestsController::class, 'destroy'])->name('delete_request.delete');
});
