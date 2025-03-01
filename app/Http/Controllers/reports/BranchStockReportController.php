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
            $branches = branches::where('branchID', auth()->user()->branchID)->get();
        }
        return view('reports.branch_stock.index', compact('branches'));
    }  

    public function data($branch)
    {
        $products = products::all();
        foreach($products as $product)
        {
            if($branch == "All")
            {
                $product->stock = getStock($product->id);
            }
            else
            {
                $product->stock = getBranchProductStock($product->id, $branch);
            }
            $purchase_price = avgPurchasePrice('all', 'all', $product->id);
            $product->stock_value = $product->stock * $purchase_price;
        }

        if($branch != "All")
        {
            $branch = branches::find($branch);
            $branch = $branch->name;
        }

        return view('reports.branch_stock.details', compact('branch', 'products'));
    }
}
