<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customerProductsSaleReport extends Controller
{
    public function index()
    {
        $customers = accounts::customer()->currentBranch()->get();
        return view('reports.customerProductsSaleReport.index', compact('customers'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $customer = $request->customer;

        $sales = sales::where('customerID', $customer)->whereBetween('date', [$from, $to])->pluck('id')->toArray();

        $sale_details = sale_details::whereIn('saleID', $sales)->select('productID', DB::raw('SUM(pc) as total_pc'), DB::raw('SUM(amount) as total_amount'))->groupBy('productID')->get();

        /* $products = []; */
        foreach($sale_details as $product){
            $product1 = products::find($product->productID);
            $product->name = $product1->name;
            $product->brand = $product1->brand->name;
            $product->category = $product1->category->name;
            $product->unit = $product1->units->first()->unit_name;
            $product->unit_value = $product1->units->first()->value;
            $product->total_pc = $product->total_pc;
            $product->total_amount = $product->total_amount;
           /*  $products[] = ['name' => $product->name, 'brand' => $product->brand, 'category' => $product->category, 'unit' => $product->unit, 'unit_value' => $product->unit_value, 'total_pc' => $product->total_pc, 'total_amount' => $product->total_amount]; */
        }

        $customer = accounts::find($customer);

       return view('reports.customerProductsSaleReport.details', compact('sale_details', 'customer', 'from', 'to'));
    }
}
