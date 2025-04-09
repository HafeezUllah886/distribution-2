<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\products;
use App\Models\warehouses;
use Illuminate\Http\Request;

class BranchStockReportController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }
        return view('reports.branch_stock.index', compact('branches'));
    }  

    public function data($branch, $value)
    {
        $products = products::currentBranch()->get();
        foreach($products as $product)
        {
            
                $product->stock = getBranchProductStock($product->id, $branch);

                if($value == 'Purchase Wise')
        {
            $product->stock_value =  branch_product_stock_value_purchase_wise($product->id, $branch);
        }
        elseif($value == 'Sale Wise')
        {
            $product->stock_value =  branch_product_stock_value_sale_wise($product->id, $branch);
        }
        else
        {
            $product->stock_value =  branch_product_stock_value_cost_wise($product->id, $branch);
            }
        }

        $branch = branches::find($branch);
        $branch = $branch->name;


        return view('reports.branch_stock.details', compact('branch', 'products', 'value'));
    }
}
