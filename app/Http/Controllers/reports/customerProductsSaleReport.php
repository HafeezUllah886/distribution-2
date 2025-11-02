<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sales;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class customerProductsSaleReport extends Controller
{
    public function index()
    {
        $customers = accounts::customer()->currentBranch()->get();
        $vendors = accounts::vendor()->currentBranch()->get();
        $areas = area::currentBranch()->get();
        $orderbookers = User::orderbookers()->currentBranch()->active()->get();
        return view('reports.customerProductsSaleReport.index', compact('customers', 'vendors', 'areas', 'orderbookers'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $customer = $request->customer ?? 'All';
        $vendor = $request->vendor ?? 'All';
        $orderbooker = $request->orderbooker ?? 'All';

        if($customer == 'All')
        {
            $customers = accounts::where('type', 'Customer')->currentBranch();
        }
        else
        {
            $customers = accounts::whereIn('id', $customer);
        }

        $customers = $customers->get();


        foreach($customers as $customer1)
        {
            if($orderbooker == 'All')
            {
                $sales = sales::where('customerID', $customer1->id)->whereBetween('date', [$from, $to])->pluck('id')->toArray();
                $orderbookerIDs = sales::where('customerID', $customer1->id)->whereBetween('date', [$from, $to])->pluck('orderbookerID')->toArray();
                $orderbooker_titles = User::whereIn('id', $orderbookerIDs)->pluck('name')->toArray();
            }
            else
            {
                $sales = sales::where('customerID', $customer1->id)->whereBetween('date', [$from, $to])->whereIn('orderbookerID', $orderbooker)->pluck('id')->toArray();
                $orderbooker_titles = User::whereIn('id', $orderbooker)->pluck('name')->toArray();
            }
           
            if($vendor == 'All')
            {
                $sale_details = sale_details::whereIn('saleID', $sales)->select('productID', DB::raw('SUM(pc) as total_pc'), DB::raw('SUM(amount) as total_amount'))->groupBy('productID')->get();
            }
            else
            {
                $sale_details = sale_details::whereIn('saleID', $sales)->whereHas('product', function($query) use ($vendor) {
                    $query->whereIn('vendorID', $vendor);
                })->select('productID', DB::raw('SUM(pc) as total_pc'), DB::raw('SUM(amount) as total_amount'))->groupBy('productID')->get();
            }
    
            
            foreach($sale_details as $product){
                $product1 = products::find($product->productID);
                $product->name = $product1->name;
                $product->brand = $product1->brand->name;
                $product->category = $product1->category->name;
                $product->unit = $product1->units->first()->unit_name;
                $product->unit_value = $product1->units->first()->value;
                $product->total_pc = $product->total_pc;
                $product->total_qty = $product->total_pc / $product->unit_value;
                $product->total_amount = $product->total_amount;
                $product->vendor = $product1->vendor->title;
            }

            // Group products by vendor while maintaining object structure
            $vendor_wise = collect($sale_details)->groupBy('vendor');

            $customer1->sales = $vendor_wise;
        }

       return view('reports.customerProductsSaleReport.details', compact('customers', 'from', 'to'));
    }
}
