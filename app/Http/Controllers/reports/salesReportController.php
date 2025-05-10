<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\sales;
use App\Models\User;
use Illuminate\Http\Request;

class salesReportController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
            $customers = accounts::customer()->get();
            $orderbookers = User::orderbookers()->get();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
            $customers = accounts::customer()->where('branchID', auth()->user()->branchID)->get();
            $orderbookers = User::orderbookers()->where('branchID', auth()->user()->branchID)->get();
        }
        return view('reports.sales.index', compact('branches', 'customers', 'orderbookers'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branch = $request->branch;
        
        if($branch == "All")
        {
            $sales = sales::with('customer', 'orderbooker', 'supplyman')->whereBetween('date', [$from, $to]);
            if($request->customer)
            {
                $sales = $sales->whereIn('customerID', $request->customer);
            }
            if($request->orderbooker)
            {
                $sales = $sales->whereIn('orderbookerID', $request->orderbooker);
            }
            $sales = $sales->get();
        }
        else
        {
            $sales = sales::with('customer', 'orderbooker', 'supplyman')->whereBetween('date', [$from, $to])->where('branchID', $branch);
            if($request->customer)
            {
                $sales = $sales->whereIn('customerID', $request->customer);
            }
            if($request->orderbooker)
            {
                $sales = $sales->whereIn('orderbookerID', $request->orderbooker);
            }
            $sales = $sales->get();
            $branch = branches::find($branch);
            $branch = $branch->name;
        }

        return view('reports.sales.details', compact('from', 'to', 'sales', 'branch'));
    }
}
