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
            if($request->branch == "All")
            {
                $topProducts = products::with('units')->withSum('saleDetails', 'qty')->withSum('saleDetails', 'amount')
                ->orderByDesc('sale_details_sum_qty');

                if($request->vendor)
                {
                    $topProducts->where('vendorID', $request->vendor);
                }
                $topProducts = $topProducts->take(200)->get();

                $topProductsArray = [];

                foreach($topProducts as $product)
                {
                    $stock = getStock($product->id);
                    $price = avgSalePrice('all', 'all', 'all', $product->id);
        
                    $topProductsArray [] = ['name' => $product->name, 'unit_value' => $product->units[0]->value, 'unit_name' => $product->units[0]->unit_name, 'price' => $price, 'stock' => $stock, 'amount' => $product->sale_details_sum_amount, 'sold' => $product->sale_details_sum_qty];
                }
            }
            else
            {
                $sales = sales::where('branchID', $request->branch)->get()->pluck('id')->toArray();
                $topProducts = products::with('units')->whereHas('saleDetails', function($query) use ($sales) {
                    $query->whereIn('saleID', $sales);
                })
                ->withSum(['saleDetails' => function($query) use ($sales) {
                    $query->whereIn('saleID', $sales);
                }], 'qty')
                ->withSum(['saleDetails' => function($query) use ($sales) {
                    $query->whereIn('saleID', $sales);
                }], 'amount')
                ->orderByDesc('sale_details_sum_qty');

                if($request->vendor)
                {
                    $topProducts->where('vendorID', $request->vendor);
                }
                $topProducts = $topProducts->take(200)->get();

                $topProductsArray = [];

            foreach($topProducts as $product)
            {
                $stock = getBranchProductStock($product->id, $request->branch);
                $price = avgSalePrice('all', 'all',$request->branch, $product->id);
    
                $topProductsArray [] = ['name' => $product->name, 'unit_value' => $product->units[0]->value, 'unit_name' => $product->units[0]->unit_name, 'price' => $price, 'stock' => $stock, 'amount' => $product->sale_details_sum_amount, 'sold' => $product->sale_details_sum_qty];
            }
            }

            if($request->branch != "All")
            {
                $branch = branches::find($request->branch);
                $branch = $branch->name;
            }

        return view('reports.top_products.details', compact('branch', 'topProductsArray'));
    }
}
