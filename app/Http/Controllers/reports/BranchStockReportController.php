<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;


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
        $vendors = accounts::vendor()->currentBranch()->get();
        return view('reports.branch_stock.index', compact('branches', 'vendors'));
    }  

    public function data($branch, $value, $vendor)
    {
        $vendorIds = array_map('intval', explode(',', $vendor));
        $vendors = accounts::with('vendor_products')->whereIn('id', $vendorIds)->get();
        foreach($vendors as $vendor)
        {
            foreach($vendor->vendor_products as $product)
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
        }
        
        $branch = branches::find($branch);
        $branch = $branch->name;


        return view('reports.branch_stock.details', compact('branch', 'value', 'vendors'));
    }
}
