<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
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
            $warehouses = warehouses::where('branchID', auth()->user()->branchID)->get();
        }

        $vendors = accounts::vendor()->currentBranch()->get();
        return view('reports.warehouse_stock.index', compact('warehouses', 'vendors'));
    }

    public function data($warehouse, $value, $vendor = 'All')
    {
        if($vendor != "All")
        {
            $vendorIds = array_map('intval', explode(',', $vendor));

            $vendors = accounts::with('vendor_products')->whereIn('id', $vendorIds)->get();
        }
        else
        {
            $vendors = accounts::vendor()->currentBranch()->get();
        }
       
        foreach($vendors as $vendor)
        {
            $products = products::currentBranch()->where('vendorID', $vendor)->get();
            foreach ($vendor->vendor_products as $product) {

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
        }
        
        $warehouse = warehouses::find($warehouse);
        $warehouse = $warehouse->name;
        return view('reports.warehouse_stock.details', compact('warehouse', 'vendors', 'value'));
    }
}
