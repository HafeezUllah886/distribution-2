<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\orderbooker_customers;
use App\Models\User;
use Illuminate\Http\Request;

class OrderbookerPerformanceReportController extends Controller
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
        return view('reports.top_orderbooker.index', compact('branches'));
    }

    public function data($branch)
    {
            if($branch == "All")
            {
                $orderbookers = User::orderbookers()->with('sales')->active()->get();
            }
            else
            {
                $orderbookers = User::orderbookers()->with('sales')->active()->where('branchID', $branch)->get();
            }

            $orderbookers = $orderbookers->sortByDesc(function ($orderbooker) {
                return $orderbooker->sales->sum('net');
            });

            foreach($orderbookers as $orderbooker)
            {
                $orderbooker->balance = getUserAccountBalance($orderbooker->id);
                $orderbooker->sales = $orderbooker->sales->sum('net');

                $customers = orderbooker_customers::where('orderbookerID', $orderbooker->id)->get();

                $customer_balance = 0;
                foreach($customers as $customer)
                {
                    $customer_balance += getAccountBalanceOrderbookerWise($customer->customerID, $orderbooker->id);
                }
                $orderbooker->customer_balance = $customer_balance;
            }

            if($branch != "All")
            {
                $branch = branches::find($branch);
                $branch = $branch->name;
            }

        return view('reports.top_orderbooker.details', compact('branch', 'orderbookers'));
    }
}
