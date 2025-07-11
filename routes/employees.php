<?php

use App\Http\Controllers\EmployeeLedgerController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\GenerateSalaryController;
use App\Http\Middleware\Admin_BranchAdmin_AccountantCheck;
use App\Http\Middleware\confirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', Admin_BranchAdmin_AccountantCheck::class)->group(function () {
   Route::resource('employees', EmployeesController::class);
   Route::resource('generate_salary', GenerateSalaryController::class);
   Route::get('generate_salary/delete/{ref}', [GenerateSalaryController::class, 'delete'])->name('generate_salary.delete')->middleware(confirmPassword::class);

   Route::get('employee/statement/{id}/{from}/{to}', [EmployeeLedgerController::class, 'statement'])->name('employee.statement');
});


