<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\products;
use App\Models\sale_details;
use Illuminate\Http\Request;

class DailyVendorWiseProductsSalesReport extends Controller
{
    public function index()
    {
        $branches = branches::all();

        if(auth()->user()->role != 'Admin')
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }
       
        return view('reports.daily_vendor_wise_products_sales.index', compact('branches'));
    }

    public function data(Request $request)
    {
        $from = $request->from ?? date('Y-m-d');
        $to = $request->to ?? date('Y-m-d');
        $branch = $request->branch;
        $vendors = accounts::with('vendor_products')->vendor()->where('branchID', $branch)->get();

        foreach($vendors as $vendor)
        {
           $products = products::where('vendorID', $vendor->id)->get();

           foreach($products as $product)
           {
               $sales = sale_details::where('productID', $product->id)->whereBetween('date', [$from, $to])->get();

               $pc = $sales->sum('pc');
               $product_unit = $product->units[0]->unit_name;
               $product_unit_value = $product->units[0]->value;
               $product_qty = intdiv($pc, $product_unit_value);
               $product_loose = $pc % $product_unit_value;
               $product_amount = $sales->sum('amount');

               $product->pc = $pc;
               $product->product_unit = $product_unit;
               $product->product_unit_value = $product_unit_value;
               $product->product_qty = $product_qty;
               $product->product_loose = $product_loose;
               $product->product_amount = $product_amount;
           }

           $vendor->products = $products;
        }

        $branch = branches::find($branch);


        return view('reports.daily_vendor_wise_products_sales.details', compact('vendors', 'branch', 'from', 'to'));
    }
}
