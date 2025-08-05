<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\products;
use App\Models\sales;
use Illuminate\Http\Request;

class TopSellingProductsReportController extends Controller
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
        return view('reports.top_products.index', compact('branches', 'vendors'));
    }  

    public function data(Request $request)
    {

        $from = $request->from;
        $to = $request->to;
        $branch = $request->branch ?? "All";
        $vendor = $request->vendor ?? "All";

        $products = products::with('units') // eager load units
        ->whereHas('saleDetails', function ($query) use ($from, $to) {
            $query->whereBetween('date', [$from, $to]); // ✅ fixed here
        })
        ->withSum(['saleDetails as pc_sum' => function ($q) use ($from, $to) {
            $q->whereBetween('date', [$from, $to]);
        }], 'pc')
        ->withSum(['saleDetails as amount_sum' => function ($q) use ($from, $to) {
            $q->whereBetween('date', [$from, $to]);
        }], 'amount');
       
        if($branch != "All")
        {
            $products->where('branchID', $branch);
        }
        if($vendor != "All")
        {
            $products->where('vendorID', $vendor);
        }
        $products = $products->orderByDesc('pc_sum')->take(200)->get();

        $topProductsArray = [];

        foreach($products as $product)
        {
            if($branch == "All")
            {
                $stock = getStock($product->id);
            }
            else
            {
                $stock = getBranchProductStock($product->id, $branch);
            }
            $price = $product->amount_sum / $product->pc_sum;
    
            $topProductsArray [] = ['name' => $product->name, 'unit_value' => $product->units[0]->value, 'unit_name' => $product->units[0]->unit_name, 'price' => $price, 'stock' => $stock, 'amount' => $product->amount_sum, 'sold' => $product->pc_sum];
        }

        if($branch != "All")
        {
            $branch = branches::where('id', $branch)->first()->name;
        }
        return view('reports.top_products.details', compact('branch', 'topProductsArray' ,'from', 'to'));
    }
}
