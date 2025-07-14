<?php

use App\Http\Controllers\EmployeeLedgerController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\EmployeesPaymentCatsController;
use App\Http\Controllers\GenerateSalaryController;
use App\Http\Controllers\IssueAdvanceController;
use App\Http\Controllers\IssueMiscController;
use App\Http\Controllers\IssueSalaryController;
use App\Http\Middleware\Admin_BranchAdmin_AccountantCheck;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', Admin_BranchAdmin_AccountantCheck::class)->group(function () {
   Route::resource('employees', EmployeesController::class);
   Route::resource('generate_salary', GenerateSalaryController::class);
   Route::get('generate_salary/delete/{ref}', [GenerateSalaryController::class, 'delete'])->name('generate_salary.delete')->middleware(confirmPassword::class);

   Route::get('employee/statement/{id}/{from}/{to}', [EmployeeLedgerController::class, 'statement'])->name('employee.statement');

   Route::resource('issue_salary', IssueSalaryController::class);
   Route::get('issue_salary/delete/{ref}', [IssueSalaryController::class, 'delete'])->name('issue_salary.delete')->middleware(confirmPassword::class);

   Route::resource('issue_advance', IssueAdvanceController::class);
   Route::get('issue_advance/delete/{ref}', [IssueAdvanceController::class, 'delete'])->name('issue_advance.delete')->middleware(confirmPassword::class);

   Route::resource('issue_misc', IssueMiscController::class);
   Route::get('issue_misc/delete/{ref}', [IssueMiscController::class, 'delete'])->name('issue_misc.delete')->middleware(confirmPassword::class);

   Route::resource('issue_misc_cats', EmployeesPaymentCatsController::class);
});


