<?php

use App\Http\Controllers\reports\BranchStockReportController;
use App\Http\Controllers\reports\profitController;
use App\Http\Controllers\reports\balanceSheetReport;
use App\Http\Controllers\reports\customerProductsSaleReport;
use App\Http\Controllers\reports\dailycashbookController;
use App\Http\Controllers\reports\DailyInvWiseProductsSalesReport;
use App\Http\Controllers\reports\DailyVendorWiseProductsSalesReport;
use App\Http\Controllers\reports\ExpenseReportController;
use App\Http\Controllers\reports\invoicePaymentsReportController;
use App\Http\Controllers\reports\loadsheetController;
use App\Http\Controllers\reports\OrderbookerPerformanceReportController;
use App\Http\Controllers\reports\OrderbookerWiseCustomerBalanceReport;
use App\Http\Controllers\reports\OrdersReportController;
use App\Http\Controllers\reports\ProductsSummaryReportController;
use App\Http\Controllers\reports\purchaseReportController;
use App\Http\Controllers\reports\salesReportController;
use App\Http\Controllers\reports\TopCustomersReportController;
use App\Http\Controllers\reports\TopSellingProductsReportController;
use App\Http\Controllers\reports\WarehouseStockReportController;
use App\Http\Controllers\reports\stockMovementReportController;
use App\Http\Controllers\reports\SupplymanLabourChargesReportController;
use App\Http\Controllers\reports\UnloaderLabourChargesReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/reports/profit', [profitController::class, 'index'])->name('reportProfit');
    Route::get('/reports/profitData', [profitController::class, 'data'])->name('reportProfitData');

    Route::get('/reports/loadsheet', [loadsheetController::class, 'index'])->name('reportLoadsheet');
    Route::get('/reports/loadsheet/{id}/{date}', [loadsheetController::class, 'data'])->name('reportLoadsheetData');

    Route::get('/reports/productSummary', [ProductsSummaryReportController::class, 'index'])->name('reportProductSummary');
    Route::get('/reports/productSummaryData', [ProductsSummaryReportController::class, 'data'])->name('reportProductSummaryData');

    Route::get('/reports/sales', [salesReportController::class, 'index'])->name('reportSales');
    Route::get('/reports/salesFilter', [salesReportController::class, 'filter'])->name('reportSalesFilter');
    Route::get('/reports/salesData', [salesReportController::class, 'data'])->name('reportSalesData');
    
    Route::get('/reports/orders', [OrdersReportController::class, 'index'])->name('reportOrders');
    Route::get('/reports/ordersFilter', [OrdersReportController::class, 'filter'])->name('reportOrdersFilter');
    Route::get('/reports/ordersData', [OrdersReportController::class, 'data'])->name('reportOrdersData');

    Route::get('/reports/purchases', [purchaseReportController::class, 'index'])->name('reportPurchases');
    Route::get('/reports/purchasesData', [purchaseReportController::class, 'data'])->name('reportPurchasesData');

    Route::get('/reports/dailycashbook', [dailycashbookController::class, 'index'])->name('reportCashbook');
    Route::get('/reports/dailycashbook/{date}', [dailycashbookController::class, 'details'])->name('reportCashbookData');

    Route::get('/reports/balanceSheet', [balanceSheetReport::class, 'index'])->name('reportBalanceSheet');
    Route::get('/reports/balanceSheet/{type}/{from}/{to}/{branch}', [balanceSheetReport::class, 'data'])->name('reportBalanceSheetData');

    Route::get('/reports/warehousestockreport', [WarehouseStockReportController::class, 'index'])->name('reportWarehouseStock');
    Route::get('/reports/warehousestockreport/{warehouse}/{value}/{vendors}', [WarehouseStockReportController::class, 'data'])->name('reportWarehouseStockData');

    Route::get('/reports/branchstockreport', [BranchStockReportController::class, 'index'])->name('reportBranchStock');
    Route::get('/reports/branchstockreport/{branch}/{value}/{vendors}', [BranchStockReportController::class, 'data'])->name('reportBranchStockData');

    Route::get('/reports/topcustomersreport', [TopCustomersReportController::class, 'index'])->name('reportTopCustomers');
    Route::get('/reports/topcustomersreportData', [TopCustomersReportController::class, 'data'])->name('reportTopCustomersData');

    Route::get('/reports/toporderbookersreport', [OrderbookerPerformanceReportController::class, 'index'])->name('reportTopOrderbookers');
    Route::get('/reports/toporderbookersreport/{branch}', [OrderbookerPerformanceReportController::class, 'data'])->name('reportTopOrderbookersData');

    Route::get('/reports/topsellingproductsreport', [TopSellingProductsReportController::class, 'index'])->name('reportTopSellingProducts');
    Route::get('/reports/topsellingproductsreportData', [TopSellingProductsReportController::class, 'data'])->name('reportTopSellingProductsData');
    
    Route::get('/reports/dailyvendorwiseproductssalesreport', [DailyVendorWiseProductsSalesReport::class, 'index'])->name('reportDailyVendorWiseProductsSales');
    Route::get('/reports/dailyvendorwiseproductssalesreportData', [DailyVendorWiseProductsSalesReport::class, 'data'])->name('reportDailyVendorWiseProductsSalesData');
    
    Route::get('/reports/dailyinvwiseproductssalesreport', [DailyInvWiseProductsSalesReport::class, 'index'])->name('reportDailyInvWiseProductsSales');
    Route::get('/reports/dailyinvwiseproductssalesreportData', [DailyInvWiseProductsSalesReport::class, 'data'])->name('reportDailyInvWiseProductsSalesData');
    
    Route::get('/reports/stockmovementreport', [stockMovementReportController::class, 'index'])->name('reportStockMovement');
    Route::get('/reports/stockmovementreportData', [stockMovementReportController::class, 'data'])->name('reportStockMovementData');

    Route::get('/reports/supplymanreport', [SupplymanLabourChargesReportController::class, 'index'])->name('reportSupplymanReport');
    Route::get('/reports/supplymanreportData', [SupplymanLabourChargesReportController::class, 'data'])->name('reportSupplymanReportData');

    Route::get('/reports/unloaderreport', [UnloaderLabourChargesReportController::class, 'index'])->name('reportUnloaderReport');
    Route::get('/reports/unloaderreportData', [UnloaderLabourChargesReportController::class, 'data'])->name('reportUnloaderReportData');

    Route::get('/reports/invoicepaymentsreport', [invoicePaymentsReportController::class, 'index'])->name('reportInvoicePayments');
    Route::get('/reports/invoicepaymentsreportData', [invoicePaymentsReportController::class, 'data'])->name('reportInvoicePaymentsData');

    Route::get('/reports/customerproductsalesreport', [customerProductsSaleReport::class, 'index'])->name('reportCustomerProductSales');
    Route::get('/reports/customerproductsalesreportData', [customerProductsSaleReport::class, 'data'])->name('reportCustomerProductSalesData');

    Route::get('/reports/orderbooker_customer_balance', [OrderbookerWiseCustomerBalanceReport::class, 'index'])->name('reportOrderbookerWiseCustomerBalance');
    Route::get('/reports/orderbooker_customer_balanceData', [OrderbookerWiseCustomerBalanceReport::class, 'data'])->name('reportOrderbookerWiseCustomerBalanceData');

    Route::get('/reports/expense', [ExpenseReportController::class, 'index'])->name('reportExpense');
    Route::get('/reports/expensedata', [ExpenseReportController::class, 'details'])->name('reportExpenseData');

    Route::get('/get-orderbookers-by-customer/{customer}', [OrderbookerWiseCustomerBalanceReport::class, 'getOrderbookersByCustomer'])->name('get-orderbookers-by-customer');
});
