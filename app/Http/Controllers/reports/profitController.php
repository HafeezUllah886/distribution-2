<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\expenses;
use App\Models\products;
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
            $branches = branches::where('branchID', auth()->user()->branchID)->get();
        }
        return view('reports.profit.index', compact('branches'));
    }

    public function data($from, $to, $branch)
    {
        $products = products::all();
        $data = [];
        foreach($products as $product)
        {
            if($branch == "All")
            {
                $purchaseRate = avgPurchasePrice($from, $to, 'all', $product->id);
                $saleRate = avgSalePrice($from, $to, 'all',$product->id);
                $sold = sale_details::where('productID', $product->id)->whereBetween('date', [$from, $to])->sum('qty');
                $ppu = $saleRate - $purchaseRate;
                $profit = $ppu * $sold;
                $stock = getStock($product->id);
                $stockValue = productStockValue($product->id);
            }
            else
            {
                $purchaseRate = avgPurchasePrice($from, $to, $branch, $product->id);
                $saleRate = avgSalePrice($from, $to, $branch, $product->id);
                $sold = sale_details::where('productID', $product->id)
                    ->whereHas('sale', function($query) use ($branch) {
                        $query->where('branchID', $branch);
                    })
                    ->whereBetween('date', [$from, $to])
                    ->sum('qty');
                $ppu = $saleRate - $purchaseRate;
                $profit = $ppu * $sold;
                $stock = getBranchProductStock($product->id, $branch);
                $stockValue = $stock * $purchaseRate;
            }

            $data[] = ['name' => $product->name, 'purchaseRate' => $purchaseRate, 'saleRate' => $saleRate, 'sold' => $sold, 'ppu' => $ppu, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue];
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
