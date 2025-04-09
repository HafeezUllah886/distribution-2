<?php

use App\Http\Controllers\reports\BranchStockReportController;
use App\Http\Controllers\reports\profitController;
use App\Http\Controllers\reports;
use App\Http\Controllers\reports\balanceSheetReport;
use App\Http\Controllers\reports\dailycashbookController;
use App\Http\Controllers\reports\loadsheetController;
use App\Http\Controllers\reports\OrderbookerPerformanceReportController;
use App\Http\Controllers\reports\ProductsSummaryReportController;

use App\Http\Controllers\reports\purchaseReportController;

use App\Http\Controllers\reports\salesReportController;
use App\Http\Controllers\reports\TopCustomersReportController;
use App\Http\Controllers\reports\TopSellingProductsReportController;
use App\Http\Controllers\reports\WarehouseStockReportController;
use App\Http\Middleware\adminCheck;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/reports/profit', [profitController::class, 'index'])->name('reportProfit');
    Route::get('/reports/profitData/{from}/{to}/{branch}', [profitController::class, 'data'])->name('reportProfitData');

    Route::get('/reports/loadsheet', [loadsheetController::class, 'index'])->name('reportLoadsheet');
    Route::get('/reports/loadsheet/{id}/{date}', [loadsheetController::class, 'data'])->name('reportLoadsheetData');

    Route::get('/reports/productSummary', [ProductsSummaryReportController::class, 'index'])->name('reportProductSummary');
    Route::get('/reports/productSummaryData/{from}/{to}/{branch}', [ProductsSummaryReportController::class, 'data'])->name('reportProductSummaryData');

    Route::get('/reports/sales', [salesReportController::class, 'index'])->name('reportSales');
    Route::get('/reports/salesData/{from}/{to}/{branch}', [salesReportController::class, 'data'])->name('reportSalesData');

    Route::get('/reports/purchases', [purchaseReportController::class, 'index'])->name('reportPurchases');
    Route::get('/reports/purchasesData/{from}/{to}/{branch}', [purchaseReportController::class, 'data'])->name('reportPurchasesData');

    Route::get('/reports/dailycashbook', [dailycashbookController::class, 'index'])->name('reportCashbook');
    Route::get('/reports/dailycashbook/{date}', [dailycashbookController::class, 'details'])->name('reportCashbookData');

    Route::get('/reports/balanceSheet', [balanceSheetReport::class, 'index'])->name('reportBalanceSheet');
    Route::get('/reports/balanceSheet/{type}/{from}/{to}/{branch}', [balanceSheetReport::class, 'data'])->name('reportBalanceSheetData');

    Route::get('/reports/warehousestockreport', [WarehouseStockReportController::class, 'index'])->name('reportWarehouseStock');
    Route::get('/reports/warehousestockreport/{warehouse}/{value}', [WarehouseStockReportController::class, 'data'])->name('reportWarehouseStockData');

    Route::get('/reports/branchstockreport', [BranchStockReportController::class, 'index'])->name('reportBranchStock');
    Route::get('/reports/branchstockreport/{branch}/{value}', [BranchStockReportController::class, 'data'])->name('reportBranchStockData');

    Route::get('/reports/topcustomersreport', [TopCustomersReportController::class, 'index'])->name('reportTopCustomers');
    Route::get('/reports/topcustomersreport/{branch}', [TopCustomersReportController::class, 'data'])->name('reportTopCustomersData');

    Route::get('/reports/toporderbookersreport', [OrderbookerPerformanceReportController::class, 'index'])->name('reportTopOrderbookers');
    Route::get('/reports/toporderbookersreport/{branch}', [OrderbookerPerformanceReportController::class, 'data'])->name('reportTopOrderbookersData');

    Route::get('/reports/topsellingproductsreport', [TopSellingProductsReportController::class, 'index'])->name('reportTopSellingProducts');
    Route::get('/reports/topsellingproductsreport/{branch}', [TopSellingProductsReportController::class, 'data'])->name('reportTopSellingProductsData');
});
