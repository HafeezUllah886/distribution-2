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
        $orderbookers = User::orderbookers()->currentBranch()->get();
        return view('reports.balanceSheet.index', compact('branches', 'areas', 'orderbookers'));
    }

    public function data($type, $from, $to, $branch, $area, $orderbooker)    
    {
            $ids = accounts::where('type', $type)->where('branchID', $branch);
            if($area != "All")
            {
                $ids = $ids->where('areaID', $area);
            }

            if($orderbooker != "All")
            {
                $orderbooker_customers = orderbooker_customers::where('orderbookerID', $orderbooker)->pluck('customerID')->toArray();
                $ids = $ids->whereIn('id', $orderbooker_customers);
            }
            $ids = $ids->pluck('id')->toArray();

            $branch = branches::find($branch);
            $branch = $branch->name;
        $transactions = transactions::with('account')->whereIn('accountID', $ids)->whereBetween('date', [$from, $to])->get();

        $pre_cr = transactions::whereIn('accountID', $ids)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = transactions::whereIn('accountID', $ids)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = transactions::whereIn('accountID', $ids)->sum('cr');
        $cur_db = transactions::whereIn('accountID', $ids)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('reports.balanceSheet.details', compact('type', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to', 'branch'));
    }
}
