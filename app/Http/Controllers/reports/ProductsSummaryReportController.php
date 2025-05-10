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
        $branch = $request->branch;
       
        if($branch == "All")
        {
            if($request->vendor)
            {
                $topProducts = products::with('units')->whereIn('vendorID', $request->vendor)->whereHas('saleDetails', function($query) use ($from, $to) {
               
                    $query->whereBetween('date', [$from, $to]);
                })
                ->withSum(['saleDetails' => function($query) use ($from, $to) {
    
                    $query->whereBetween('date', [$from, $to]);
                }], 'qty')
                ->withSum(['saleDetails' => function($query) use ($from, $to) {
                    $query->whereBetween('date', [$from, $to]);
                }], 'amount')
                ->orderByDesc('sale_details_sum_qty')
                ->get();
            }
            else
            {
                $topProducts = products::with('units')->whereHas('saleDetails', function($query) use ($from, $to) {
               
                    $query->whereBetween('date', [$from, $to]);
                })
                ->withSum(['saleDetails' => function($query) use ($from, $to) {
    
                    $query->whereBetween('date', [$from, $to]);
                }], 'qty')
                ->withSum(['saleDetails' => function($query) use ($from, $to) {
                    $query->whereBetween('date', [$from, $to]);
                }], 'amount')
                ->orderByDesc('sale_details_sum_qty')
                ->get();
            }
           
    
            $topProductsArray = [];
    
            foreach($topProducts as $product)
            {
                $stock = getStock($product->id);
                $price = avgSalePrice($from, $to,'all', $product->id);
            $pprice = avgPurchasePrice($from, $to, 'all', $product->id);
                
                $ppu = $price - $pprice;
                $profit = $ppu * $product->sale_details_sum_qty;
                $stockValue = stockValue($product->id);

                $topProductsArray[] = ['name' => $product->name, 'unit' => $product->units[0]->unit_name, 'unitValue' => $product->units[0]->value, 'price' => $price, 'pprice' => $pprice, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue, 'amount' => $product->sale_details_sum_amount, 'sold' => $product->sale_details_sum_qty];
            }
        }
       else
       {
            $sales = sales::where('branchID', $branch)->get()->pluck('id')->toArray();
            if($request->vendor)
            {
                $topProducts = products::with('units')->whereIn('vendorID', $request->vendor)->whereHas('saleDetails', function($query) use ($sales, $from, $to) {
                    $query->whereIn('saleID', $sales);
                    $query->whereBetween('date', [$from, $to]);
                })
                ->withSum(['saleDetails' => function($query) use ($sales, $from, $to) {
                    $query->whereIn('saleID', $sales);
                    $query->whereBetween('date', [$from, $to]);
                }], 'qty')
                ->withSum(['saleDetails' => function($query) use ($sales, $from, $to) {
                    $query->whereIn('saleID', $sales);
                    $query->whereBetween('date', [$from, $to]);
                }], 'amount')
                ->orderByDesc('sale_details_sum_qty')
                ->get();
            }
            else
            {
                $topProducts = products::with('units')->whereHas('saleDetails', function($query) use ($sales, $from, $to) {
                    $query->whereIn('saleID', $sales);
                    $query->whereBetween('date', [$from, $to]);
                })
                ->withSum(['saleDetails' => function($query) use ($sales, $from, $to) {
                    $query->whereIn('saleID', $sales);
                    $query->whereBetween('date', [$from, $to]);
                }], 'qty')
                ->withSum(['saleDetails' => function($query) use ($sales, $from, $to) {
                    $query->whereIn('saleID', $sales);
                    $query->whereBetween('date', [$from, $to]);
                }], 'amount')
                ->orderByDesc('sale_details_sum_qty')
                ->get();
            }
            $topProductsArray = [];

            foreach($topProducts as $product)
            {
                $stock = getBranchProductStock($product->id, $branch);
                $price = avgSalePrice($from, $to,$branch, $product->id);
            $pprice = avgPurchasePrice($from, $to, $branch, $product->id);
            
            $ppu = $price - $pprice;
            $profit = $ppu * $product->sale_details_sum_qty;
            $stockValue = getBranchProductStock($product->id, $branch) * $pprice;

            $topProductsArray[] = ['name' => $product->name, 'unit' => $product->units[0]->unit_name, 'unitValue' => $product->units[0]->value, 'price' => $price, 'pprice' => $pprice, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue, 'amount' => $product->sale_details_sum_amount, 'sold' => $product->sale_details_sum_qty];
        } 
       }

       if($branch != "All")
       {
           $branch = branches::find($branch);
           $branch = $branch->name;
       }

        return view('reports.products_summary.details', compact('topProductsArray', 'branch', 'from', 'to'));
    }
}
