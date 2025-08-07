<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\products;
use App\Models\sales;
use Illuminate\Http\Request;

class ProductsSummaryReportController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
            $vendors = accounts::vendor()->get();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
            $vendors = accounts::vendor()->where('branchID', auth()->user()->branchID)->get();
        }
        return view('reports.products_summary.index', compact('branches', 'vendors'));
    }

    public function data(request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branch = $request->branch ?? "All";
        $vendor = $request->vendor ?? "All";

        $products = products::with('units')->whereHas('saleDetails', function($query) use ($from, $to) {
               
            $query->whereBetween('date', [$from, $to]);
        })
        ->withSum(['saleDetails' => function($query) use ($from, $to) {

            $query->whereBetween('date', [$from, $to]);
        }], 'pc')
        ->withSum(['saleDetails' => function($query) use ($from, $to) {
            $query->whereBetween('date', [$from, $to]);
        }], 'amount');

        if($branch != "All")
        {
            $products->where('branchID', $branch);
        }
        if($vendor != "All")
        {
            $products->whereIn('vendorID', $vendor);
        }
        $products = $products->orderByDesc('sale_details_sum_pc')->get();
       
        $topProductsArray = [];
    
        foreach($products as $product)
        {
            $stock = getStock($product->id);
            $price = $product->sale_details_sum_amount / $product->sale_details_sum_pc;
            $pprice = avgPurchasePrice($from, $to, 'all', $product->id);
            
            $ppu = $price - $pprice;
            $profit = $ppu * $product->sale_details_sum_pc;
            $stockValue = stockValue($product->id);

            $topProductsArray[] = ['name' => $product->name, 'vendor' => $product->vendor->title, 'unit' => $product->units[0]->unit_name, 'unitValue' => $product->units[0]->value, 'price' => $price, 'pprice' => $pprice, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue, 'amount' => $product->sale_details_sum_amount, 'sold' => $product->sale_details_sum_pc];
        }

       if($branch != "All")
       {
           $branch = branches::find($branch);
           $branch = $branch->name;
       }

        return view('reports.products_summary.details', compact('topProductsArray', 'branch', 'from', 'to'));
    }
}
