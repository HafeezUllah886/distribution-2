<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use Illuminate\Http\Request;

class TopCustomersReportController extends Controller
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
        return view('reports.top_customers.index', compact('branches'));
    }  

    public function data($branch)
    {
            if($branch == "All")
            {
                $customers = accounts::customer()->with('sale')->get();
            }
            else
            {
                $customers = accounts::customer()->with('sale')->where('branchID', $branch)->get();
            }

            $customers = $customers->sortByDesc(function ($customer) {
                return $customer->sale->sum('net');
            })->take(10); // Limit

            foreach($customers as $customer)
            {
                $customer->balance = getAccountBalance($customer->id);
                $customer->sales = $customer->sale->sum('net');
            }

            if($branch != "All")
            {
                $branch = branches::find($branch);
                $branch = $branch->name;
            }

        return view('reports.top_customers.details', compact('branch', 'customers'));
    }
}
