<?php

use App\Http\Controllers\AccountsAdjustmentController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\authController;
use App\Http\Controllers\BulkInvoicePaymentsReceivingController;
use App\Http\Controllers\CurrencymgmtController;
use App\Http\Controllers\CustomerPaymentsController;
use App\Http\Controllers\DepositWithdrawController;
use App\Http\Controllers\ExpenseCategoriesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\LaborPaymentsController;
use App\Http\Controllers\PaymentReceivingController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PaymentsReceivingController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\StaffAmountAdjustmentController;
use App\Http\Controllers\StaffPaymentsController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\VendorPaymentsController;
use App\Http\Middleware\Admin_BranchAdmin_AccountantCheck;
use App\Http\Middleware\adminCheck;
use App\Http\Middleware\confirmPassword;
use App\Models\attachment;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', Admin_BranchAdmin_AccountantCheck::class)->group(function () {
    Route::get('account/view/{filter}', [AccountsController::class, 'index'])->name('accountsList');
    Route::get('account/statement/{id}/{from}/{to}', [AccountsController::class, 'show'])->name('accountStatement');
    Route::get('account/status/{id}', [AccountsController::class, 'status'])->name('account.status');
    Route::resource('account', AccountsController::class);

    Route::resource('transfers', TransferController::class);
    Route::get('transfer/delete/{ref}', [TransferController::class, 'delete'])->name('transfers.delete')->middleware(confirmPassword::class);

    Route::resource('expenses', ExpensesController::class);
    Route::get('expense/delete/{ref}', [ExpensesController::class, 'delete'])->name('expense.delete')->middleware(confirmPassword::class);

    Route::resource('expense_categories', ExpenseCategoriesController::class);

    Route::resource('payments', PaymentsController::class);
    Route::get('payments/delete/{ref}', [PaymentsController::class, 'delete'])->name('payments.delete')->middleware(confirmPassword::class);

    Route::resource('payments_receiving', PaymentsReceivingController::class);
    Route::get('payments_receiving/delete/{ref}', [PaymentsReceivingController::class, 'delete'])->name('payments_receiving.delete')->middleware(confirmPassword::class);

    Route::get('currency/details/{id}', [CurrencymgmtController::class, 'details'])->name('currency.details');
    Route::get('currency/statement/{id}/{user}/{from}/{to}', [CurrencymgmtController::class, 'show'])->name('currency.statement');

    Route::resource('staff_payments', StaffPaymentsController::class);
    Route::get('staff_payments/delete/{ref}', [StaffPaymentsController::class, 'delete'])->name('staff_payments.delete')->middleware(confirmPassword::class);

    Route::resource('accounts_adjustments', AccountsAdjustmentController::class);
    Route::get('accounts_adjustments/delete/{ref}', [AccountsAdjustmentController::class, 'delete'])->name('accounts_adjustments.delete')->middleware(confirmPassword::class);

    Route::resource('staff_amounts_adjustments', StaffAmountAdjustmentController::class);
    Route::get('staff_amounts_adjustments/delete/{ref}', [StaffAmountAdjustmentController::class, 'delete'])->name('staff_amounts_adjustments.delete')->middleware(confirmPassword::class);

    Route::resource('bulk_payment', BulkInvoicePaymentsReceivingController::class);

    Route::get('/accountbalance/{id}', function ($id) {
        // Call your Laravel helper function here
        $result = getAccountBalance($id);

        return response()->json(['data' => $result]);
    });

    Route::get("/attachment/{ref}", function($ref)
    {
        $attachment = attachment::where("refID", $ref)->first();
        if(!$attachment)
        {
            return redirect()->back()->with('error', "No Attachement Found");
        }

        return response()->file(public_path($attachment->path));
    })->name('viewAttachment');

});

