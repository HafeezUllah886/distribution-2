<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\branches;
use App\Models\orderbooker_customers;
use App\Models\transactions;
use App\Models\User;
use Illuminate\Http\Request;

class balanceSheetReport extends Controller
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
        $areas = area::currentBranch()->get();
        $orderbookers = User::orderbookers()->currentBranch()->active()->get();
        return view('reports.balanceSheet.index', compact('branches', 'areas', 'orderbookers'));
    }

    public function data($type, $area, $orderbooker)    
    {
            $ids = accounts::where('type', $type)->where('branchID', auth()->user()->branchID);
            if($type == 'Customer' && $area != "All")
            {
                $ids = $ids->where('areaID', $area);
            }

            if($type == "Customer" && $orderbooker != "All")
            {
                $orderbooker_customers = orderbooker_customers::where('orderbookerID', $orderbooker)->pluck('customerID')->toArray();
                $ids = $ids->whereIn('id', $orderbooker_customers);
            }
            $ids = $ids->pluck('id')->toArray();

            $accounts = accounts::whereIn('id', $ids)->get();

            foreach($accounts as $account)
            {
                if($type != "Customer")
                {
                    $balance = getAccountBalance($account->id);
                }
                else
                {
                    if($orderbooker != "All")
                    {
                        $balance = getAccountBalanceOrderbookerWise($account->id, $orderbooker);
                    }
                    else
                    {
                        $balance = getAccountBalance($account->id);
                    }
                }

                $account->balance = $balance;
            }

            if($area != "All")
            {
                $area = area::find($area)->name;
            }

            if($orderbooker != "All")
            {
                $orderbooker = User::find($orderbooker)->name;
            }


        return view('reports.balanceSheet.details', compact('type', 'area', 'orderbooker', 'accounts'));
    }
}
