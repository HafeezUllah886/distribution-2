<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sales;
use Illuminate\Http\Request;

class DailyInvWiseProductsSalesReport extends Controller
{
    public function index()
    {
        $branches = branches::all();

        if(auth()->user()->role != 'Admin')
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }
       
        return view('reports.daily_inv_wise_products_sales.index', compact('branches'));
    }

    public function data(Request $request)
    {
        $from = $request->from ?? date('Y-m-d');
        $to = $request->to ?? date('Y-m-d');
        $branch = $request->branch;
        $sales = sales::with('customer', 'details', 'orderbooker', 'supplyman')->where('branchID', $branch)->whereBetween('date', [$from, $to])->get();
        $branch = branches::find($branch);
        return view('reports.daily_inv_wise_products_sales.details', compact('sales', 'branch', 'from', 'to'));
    }
}
