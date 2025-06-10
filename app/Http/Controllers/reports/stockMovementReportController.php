<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\stock;
use App\Models\warehouses;
use Illuminate\Http\Request;

class stockMovementReportController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = warehouses::currentBranch()->get();

        return view('reports.stockMovement.index', compact('warehouses'));
    }

    public function data(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? now();
        $warehouse = $request->warehouse ?? warehouses::where('branchID', auth()->user()->branchID)->first()->id;
        $products = products::active()->get();

        foreach($products as $product){

            $opening_stock = stock::where('productID', $product->id)->where('warehouseID', $warehouse)->where('date', '<', $from)->sum('cr') - stock::where('productID', $product->id)->where('warehouseID', $warehouse)->where('date', '<', $from)->sum('db');
            $stock_in = stock::where('productID', $product->id)->where('warehouseID', $warehouse)->whereBetween('date', [$from, $to])->sum('cr');
            $stock_out = stock::where('productID', $product->id)->where('warehouseID', $warehouse)->whereBetween('date', [$from, $to])->sum('db');
            $closing_stock = $opening_stock + $stock_in - $stock_out;
            $current_stock = getWarehouseProductStock($product->id, $warehouse);

            $product->opening_stock = $opening_stock;
            $product->stock_in = $stock_in;
            $product->stock_out = $stock_out;
            $product->closing_stock = $closing_stock;
            $product->current_stock = $current_stock;
        }
        $warehouse = warehouses::find($warehouse);
        return view('reports.stockMovement.details', compact('products', 'from', 'to', 'warehouse'));
    }
}
