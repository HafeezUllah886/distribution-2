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
        if (auth()->user()->role == "Admin") {
            $warehouses = warehouses::all();
        } else {
            $warehouses = warehouses::where('id', auth()->user()->branchID)->get();
        }
        return view('reports.warehouse_stock.index', compact('warehouses'));
    }

    public function data($warehouse, $value)
    {
        $products = products::currentBranch()->get();
        foreach ($products as $product) {

            $product->stock = getWarehouseProductStock($product->id, $warehouse);
            $warehouse1 = warehouses::find($warehouse);

            if($value == 'Purchase Wise')
            {
                $product->stock_value =  warehouse_product_stock_value_purchase_wise($product->id, $warehouse);
            }
            elseif($value == 'Sale Wise')
            {
                $product->stock_value =  warehouse_product_stock_value_sale_wise($product->id, $warehouse);
            }
            else
            {
                $product->stock_value =  warehouse_product_stock_value_cost_wise($product->id, $warehouse);
            }
        }
        $warehouse = warehouses::find($warehouse);
        $warehouse = $warehouse->name;


        return view('reports.warehouse_stock.details', compact('warehouse', 'products', 'value'));
    }
}
