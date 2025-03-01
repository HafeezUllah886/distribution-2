<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\warehouses;
use Illuminate\Http\Request;

class WarehouseStockReportController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == "Admin")
        {
            $warehouses = warehouses::all();
        }
        else
        {
            $warehouses = warehouses::where('branchID', auth()->user()->branchID)->get();
        }
        return view('reports.warehouse_stock.index', compact('warehouses'));
    }  

    public function data($warehouse)
    {
        $products = products::all();
        foreach($products as $product)
        {
            if($warehouse == "All")
            {
                $product->stock = getStock($product->id);
            }
            else
            {
                $product->stock = getWarehouseProductStock($product->id, $warehouse);
            }
            $purchase_price = avgPurchasePrice('all', 'all', $product->id);
            $product->stock_value = $product->stock * $purchase_price;
        }

        if($warehouse != "All")
        {
            $warehouse = warehouses::find($warehouse);
            $warehouse = $warehouse->name;
        }

        return view('reports.warehouse_stock.details', compact('warehouse', 'products'));
    }
}
