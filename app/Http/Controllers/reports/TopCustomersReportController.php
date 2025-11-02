<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\orderbooker_customers;
use App\Models\User;
use Illuminate\Http\Request;

class TopCustomersReportController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
            $orderbookers = User::orderbookers()->active()->get();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
            $orderbookers = User::orderbookers()->where('branchID', auth()->user()->active()->branchID)->get();
        }
        return view('reports.top_customers.index', compact('branches', 'orderbookers'));
    }  

    public function data(Request $request)
    {
            if($request->branch == "All")
            {
                $customers = accounts::customer()->with('sale');
                if($request->orderbooker)
                {
                   $orderbooker_customers = orderbooker_customers::whereIn('orderbookerID', $request->orderbooker)->pluck('customerID');
                   $customers = $customers->whereIn('id', $orderbooker_customers);
                }
                $customers = $customers->get();
            }
            else
            {
                $customers = accounts::customer()->with('sale')->where('branchID', $request->branch);
                if($request->orderbooker)
                {
                   $orderbooker_customers = orderbooker_customers::whereIn('orderbookerID', $request->orderbooker)->pluck('customerID');
                   $customers = $customers->whereIn('id', $orderbooker_customers);
                }
                $customers = $customers->get();
            }

            $customers = $customers->sortByDesc(function ($customer) {
                return $customer->sale->sum('net');
            })->take(10); // Limit

            foreach($customers as $customer)
            {
                $customer->balance = getAccountBalance($customer->id);
                $customer->sales = $customer->sale->sum('net');
            }

            if($request->branch != "All")
            {
                $branch = branches::find($request->branch);
                $branch = $branch->name;
            }

        return view('reports.top_customers.details', compact('branch', 'customers'));
    }
}
