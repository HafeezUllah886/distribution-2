<?php

use App\Http\Controllers\AccountsAdjustmentController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\authController;
use App\Http\Controllers\AutoStaffPaymentsController;
use App\Http\Controllers\BulkInvoicePaymentsReceivingController;
use App\Http\Controllers\ChequesController;
use App\Http\Controllers\CurrencymgmtController;
use App\Http\Controllers\CustomerAdvancePaymentController;
use App\Http\Controllers\CustomerPaymentsController;
use App\Http\Controllers\DepositWithdrawController;
use App\Http\Controllers\ExpenseCategoriesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\LaborPaymentsController;
use App\Http\Controllers\MyBalanceController;
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
    Route::get('account/statement/{id}/{from}/{to}/{orderbooker?}', [AccountsController::class, 'show'])->name('accountStatement');
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

    Route::get('auto_staff_payments', [AutoStaffPaymentsController::class, 'index'])->name('auto_staff_payments');
    Route::get('auto_staff_payments/create', [AutoStaffPaymentsController::class, 'create'])->name('auto_staff_payments.create');
    Route::post('auto_staff_payments/store', [AutoStaffPaymentsController::class, 'store'])->name('auto_staff_payments.store');

    Route::resource('accounts_adjustments', AccountsAdjustmentController::class);
    Route::get('accounts_adjustments/delete/{ref}', [AccountsAdjustmentController::class, 'delete'])->name('accounts_adjustments.delete')->middleware(confirmPassword::class);

    Route::resource('staff_amounts_adjustments', StaffAmountAdjustmentController::class);
    Route::get('staff_amounts_adjustments/delete/{ref}', [StaffAmountAdjustmentController::class, 'delete'])->name('staff_amounts_adjustments.delete')->middleware(confirmPassword::class);

    Route::resource('bulk_payment', BulkInvoicePaymentsReceivingController::class);
    Route::get('bulk_payment/delete/{ref}', [BulkInvoicePaymentsReceivingController::class, 'destroy'])->name('bulk_payment.delete')->middleware(confirmPassword::class);

    Route::resource('cheques', ChequesController::class);
    Route::get('cheques/status/{id}/{status}', [ChequesController::class, 'show'])->name('cheques.status');
    Route::get('cheque/forward', [ChequesController::class, 'forward'])->name('cheques.forward');

    Route::get('staff_balance/{staff}', [MyBalanceController::class, 'staff_balance'])->name('staff_balance');

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


    Route::resource('customer_advances', CustomerAdvancePaymentController::class);
    Route::get('customer_advance/pay/{id}', [CustomerAdvancePaymentController::class, 'pay'])->name('customer_advance.pay');
    Route::get('customer_advance/delete/{id}', [CustomerAdvancePaymentController::class, 'delete'])->name('customer_advance.delete')->middleware(confirmPassword::class);
    Route::get('customer_advance/getBills', [CustomerAdvancePaymentController::class, 'getBills'])->name('customer_advance.getBills');
    Route::post('customer_advance/save_consumption', [CustomerAdvancePaymentController::class, 'save_consumption'])->name('customer_advance.save_consumption');
});

Route::middleware('auth')->group(function () {
    
    Route::get('my_balance', [MyBalanceController::class, 'index'])->name('my_balance');
    Route::get('method/statement/{user}/{method}/{from}/{to}', [AccountsController::class, 'methodStatement'])->name('methodStatement');
    Route::get('/get-customers-by-area/{area}', [BulkInvoicePaymentsReceivingController::class, 'getCustomersByArea'])->name('get-customers-by-area');
});
