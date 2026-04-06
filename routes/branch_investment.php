<?php

use App\Http\Controllers\AccountsAdjustmentController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AutoStaffPaymentsController;
use App\Http\Controllers\BranchInvestmentController;
use App\Http\Controllers\BulkInvoicePaymentsReceivingController;
use App\Http\Controllers\ChequesController;
use App\Http\Controllers\CurrencymgmtController;
use App\Http\Controllers\CustomerAdvancePaymentController;
use App\Http\Controllers\ExpenseCategoriesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\MyBalanceController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PaymentsReceivingController;
use App\Http\Controllers\StaffAmountAdjustmentController;
use App\Http\Controllers\StaffPaymentsController;
use App\Http\Controllers\TransferController;
use App\Http\Middleware\Admin_BranchAdmin_AccountantCheck;
use App\Http\Middleware\confirmPassword;
use App\Models\attachment;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', Admin_BranchAdmin_AccountantCheck::class)->group(function () {
    
    Route::resource('branch_investment', BranchInvestmentController::class);
    Route::get('branch_investment/delete/{ref}', [BranchInvestmentController::class, 'destroy'])->name('branch_investment.delete')->middleware(confirmPassword::class);

});

