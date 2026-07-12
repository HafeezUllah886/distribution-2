<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\obsolete_stock;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\returnsDetails;
use App\Models\sale_details;
use App\Models\stock;
use App\Models\stockAdjustment;
use App\Models\warehouses;
use Illuminate\Http\Request;

class stockMovementReportController extends Controller
{
    public function index(Request $request)
    {
        $branches = branches::all();
        if(auth()->user()->role != "Admin")
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }

        return view('reports.stockMovement.index', compact('branches'));
    }

    public function data(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? now();
        $branch = $request->branch;
        $products = products::active()->get();
        $warehouses = warehouses::where('branchID', $branch)->pluck('id')->toArray();

        $opening_stock_cr = stock::whereIn('warehouseID', $warehouses)->where('date', '<', $from)->groupBy('productID')->selectRaw('productID, sum(cr) as total')->pluck('total', 'productID')->toArray();
        $opening_stock_db = stock::whereIn('warehouseID', $warehouses)->where('date', '<', $from)->groupBy('productID')->selectRaw('productID, sum(db) as total')->pluck('total', 'productID')->toArray();
        
        $purchased_all = purchase_details::where('branchID', $branch)->whereBetween('date', [$from, $to])->groupBy('productID')->selectRaw('productID, sum(pc) as total')->pluck('total', 'productID')->toArray();
        $returned_all = returnsDetails::where('branchID', $branch)->whereBetween('date', [$from, $to])->groupBy('productID')->selectRaw('productID, sum(pc) as total')->pluck('total', 'productID')->toArray();
        $stock_in_Adjustments_all = stockAdjustment::where('branchID', $branch)->where('type', 'Stock-In')->whereBetween('date', [$from, $to])->groupBy('productID')->selectRaw('productID, sum(pc) as total')->pluck('total', 'productID')->toArray();
        
        $sales_all = sale_details::where('branchID', $branch)->whereBetween('date', [$from, $to])->groupBy('productID')->selectRaw('productID, sum(pc) as total')->pluck('total', 'productID')->toArray();
        $stock_out_Adjustments_all = stockAdjustment::where('branchID', $branch)->where('type', 'Stock-Out')->whereBetween('date', [$from, $to])->groupBy('productID')->selectRaw('productID, sum(pc) as total')->pluck('total', 'productID')->toArray();
        $obsolete_all = obsolete_stock::where('branchID', $branch)->whereBetween('date', [$from, $to])->groupBy('productID')->selectRaw('productID, sum(pc) as total')->pluck('total', 'productID')->toArray();

        $current_stocks_cr = stock::whereIn('warehouseID', $warehouses)->groupBy('productID')->selectRaw('productID, sum(cr) as total')->pluck('total', 'productID')->toArray();
        $current_stocks_db = stock::whereIn('warehouseID', $warehouses)->groupBy('productID')->selectRaw('productID, sum(db) as total')->pluck('total', 'productID')->toArray();

        foreach($products as $product){
            $id = $product->id;

            $opening_stock = ($opening_stock_cr[$id] ?? 0) - ($opening_stock_db[$id] ?? 0);
            
            $purchased = $purchased_all[$id] ?? 0;
            $returned = $returned_all[$id] ?? 0;
            $stock_in_Adjustments = $stock_in_Adjustments_all[$id] ?? 0;

            $totalStockIn = $purchased + $stock_in_Adjustments + $returned;

            $sales = $sales_all[$id] ?? 0;
            $stock_out_Adjustments = $stock_out_Adjustments_all[$id] ?? 0;
            $obsolete = $obsolete_all[$id] ?? 0;

            $totalStockOut = $sales + $stock_out_Adjustments + $obsolete;

            $closing_stock = $opening_stock + $totalStockIn - $totalStockOut;
            $current_stock = ($current_stocks_cr[$id] ?? 0) - ($current_stocks_db[$id] ?? 0);

            $product->opening_stock = $opening_stock;
            $product->stock_in = $totalStockIn;
            $product->stock_out = $totalStockOut;
            $product->purchased = $purchased;
            $product->returned = $returned;
            $product->total_stock_in = $totalStockIn;
            $product->total_stock_out = $totalStockOut;
            $product->sales = $sales;
            $product->obsolete = $obsolete;
            $product->stock_adjustment_in = $stock_in_Adjustments;
            $product->stock_adjustment_out = $stock_out_Adjustments;
            $product->closing_stock = $closing_stock;
            $product->current_stock = $current_stock;
        }
        $branch = branches::find($branch);
        return view('reports.stockMovement.details', compact('products', 'from', 'to', 'branch'));
    }
}
