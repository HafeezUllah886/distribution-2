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

        foreach($products as $product){

            $opening_stock = stock::where('productID', $product->id)->whereIn('warehouseID', $warehouses)->where('date', '<', $from)->sum('cr') - stock::where('productID', $product->id)->whereIn('warehouseID', $warehouses)->where('date', '<', $from)->sum('db');
            
            $purchased = purchase_details::where('productID', $product->id)->where('branchID', $branch)->whereBetween('date', [$from, $to])->sum('pc');
            $returned = returnsDetails::where('productID', $product->id)->where('branchID', $branch)->whereBetween('date', [$from, $to])->sum('pc');
            $stock_in_Adjustments = stockAdjustment::where('productID', $product->id)->where('branchID', $branch)->where('type', 'Stock-In')->whereBetween('date', [$from, $to])->sum('pc');

            $totalStockIn = $purchased + $stock_in_Adjustments + $returned;


            $sales = sale_details::where('productID', $product->id)->where('branchID', $branch)->whereBetween('date', [$from, $to])->sum('pc');
            $stock_out_Adjustments = stockAdjustment::where('productID', $product->id)->where('branchID', $branch)->where('type', 'Stock-Out')->whereBetween('date', [$from, $to])->sum('pc');
            $obsolete = obsolete_stock::where('productID', $product->id)->where('branchID', $branch)->whereBetween('date', [$from, $to])->sum('pc');

            $totalStockOut = $sales + $stock_out_Adjustments + $obsolete;

            $closing_stock = $opening_stock + $totalStockIn - $totalStockOut;
            $current_stock = getBranchProductStock($product->id, $branch);

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
