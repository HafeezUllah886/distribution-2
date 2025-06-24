<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\expenses;
use App\Models\products;
use App\Models\returnsDetails;
use App\Models\sale_details;
use Illuminate\Http\Request;

class profitController extends Controller
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
        return view('reports.profit.index', compact('branches', 'vendors'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branch = $request->branch;
        $vendor = $request->vendor;
        $products = products::query();
        if($vendor)
        {
            $products->whereIn('vendorID', $vendor);
        }
        $products = $products->get();
        $data = [];
        foreach($products as $product)
        {
            $unit = $product->units->first()->value;
            $unit_name = $product->units->first()->unit_name;
            if($branch == "All")
            {
                $purchaseRate = avgPurchasePrice($from, $to, 'all', $product->id) * $unit;
                $saleRate = avgSalePrice($from, $to, 'all',$product->id) * $unit;
                $sold = sale_details::where('productID', $product->id)->whereBetween('date', [$from, $to])->sum('qty') - returnsDetails::where('productID', $product->id)->whereBetween('date', [$from, $to])->sum('qty');
                $ppu = $saleRate - $purchaseRate;
                $profit = $ppu * $sold;
                $stock = getStock($product->id);
                $stockValue = productStockValue($product->id);
            }
            else
            {
                $purchaseRate = avgPurchasePrice($from, $to, $branch, $product->id) * $unit;
                $saleRate = avgSalePrice($from, $to, $branch, $product->id) * $unit;
                $sold = sale_details::where('productID', $product->id)
                    ->whereHas('sale', function($query) use ($branch) {
                        $query->where('branchID', $branch);
                    })
                    ->whereBetween('date', [$from, $to])
                    ->sum('qty') - returnsDetails::where('productID', $product->id)
                    ->whereHas('return', function($query) use ($branch) {
                        $query->where('branchID', $branch);
                    })
                    ->whereBetween('date', [$from, $to])
                    ->sum('qty');
                $ppu = $saleRate - $purchaseRate;
                $profit = $ppu * $sold;
                $stock = getBranchProductStock($product->id, $branch);
                $purchaseRatePC = avgPurchasePrice($from, $to, $branch, $product->id);
                $stockValue = $stock * $purchaseRatePC;
            }

            $data[] = ['name' => $product->name, 'purchaseRate' => $purchaseRate, 'saleRate' => $saleRate, 'sold' => $sold, 'ppu' => $ppu, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue, 'unit' => $unit_name, 'unit_value' => $unit];
        }

       if($branch == "All")
       {
        $expenses = expenses::whereBetween('date', [$from, $to])->sum('amount');
       }
       else
       {
        $expenses = expenses::where('branchID', $branch)->whereBetween('date', [$from, $to])->sum('amount');
        $branch = branches::find($branch);
           $branch = $branch->name;
       }

        return view('reports.profit.details', compact('from', 'to', 'data', 'expenses', 'branch'));
    }
}
