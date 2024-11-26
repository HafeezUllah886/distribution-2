<?php

use App\Http\Controllers\reports\profitController;
use App\Http\Controllers\reports;
use App\Http\Controllers\reports\balanceSheetReport;
use App\Http\Controllers\reports\comparisonReportController;
use App\Http\Controllers\reports\dailycashbookController;
use App\Http\Controllers\reports\loadsheetController;
use App\Http\Controllers\reports\productSummaryReport;
use App\Http\Controllers\reports\purchaseGstReportController;
use App\Http\Controllers\reports\purchaseReportController;
use App\Http\Controllers\reports\salesGstReportController;
use App\Http\Controllers\reports\salesManReportController;
use App\Http\Controllers\reports\salesReportController;
use App\Http\Middleware\adminCheck;
use Illuminate\Support\Facades\Route;

Route::middleware('auth', adminCheck::class)->group(function () {

    Route::get('/reports/profit', [profitController::class, 'index'])->name('reportProfit');
    Route::get('/reports/profitData/{from}/{to}', [profitController::class, 'data'])->name('reportProfitData');

    Route::get('/reports/loadsheet', [loadsheetController::class, 'index'])->name('reportLoadsheet');
    Route::get('/reports/loadsheet/{id}/{date}', [loadsheetController::class, 'data'])->name('reportLoadsheetData');

    Route::get('/reports/salesGst', [salesGstReportController::class, 'index'])->name('reportSalesGst');
    Route::get('/reports/salesGstData/{from}/{to}', [salesGstReportController::class, 'data'])->name('reportSalesGstData');

    Route::get('/reports/purchasesGst', [purchaseGstReportController::class, 'index'])->name('reportPurchasesGst');
    Route::get('/reports/purchasesGstData/{from}/{to}', [purchaseGstReportController::class, 'data'])->name('reportPurchasesGstData');

    Route::get('/reports/productSummary', [productSummaryReport::class, 'index'])->name('reportProductSummary');
    Route::get('/reports/productSummaryData/{from}/{to}', [productSummaryReport::class, 'data'])->name('reportProductSummaryData');

    Route::get('/reports/sales', [salesReportController::class, 'index'])->name('reportSales');
    Route::get('/reports/salesData/{from}/{to}/{type}', [salesReportController::class, 'data'])->name('reportSalesData');

    Route::get('/reports/purchases', [purchaseReportController::class, 'index'])->name('reportPurchases');
    Route::get('/reports/purchasesData/{from}/{to}', [purchaseReportController::class, 'data'])->name('reportPurchasesData');

    Route::get('/reports/dailycashbook', [dailycashbookController::class, 'index'])->name('reportCashbook');
    Route::get('/reports/dailycashbook/{date}', [dailycashbookController::class, 'details'])->name('reportCashbookData');

    Route::get('/reports/balanceSheet', [balanceSheetReport::class, 'index'])->name('reportBalanceSheet');
    Route::get('/reports/balanceSheet/{type}/{from}/{to}', [balanceSheetReport::class, 'data'])->name('reportBalanceSheetData');

    Route::get('/reports/comparison', [comparisonReportController::class, 'index'])->name('reportComparison');
    Route::get('/reports/comparison/{from1}/{to1}/{from2}/{to2}/{customer}', [comparisonReportController::class, 'data'])->name('reportComparisonData');
});
