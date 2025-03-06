<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\sales;
use Illuminate\Http\Request;

class salesReportController extends Controller
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
        return view('reports.sales.index', compact('branches'));
    }

    public function data($from, $to, $branch)
    {
        if($branch == "All")
        {
            $sales = sales::with('customer', 'orderbooker', 'supplyman')->whereBetween('date', [$from, $to])->get();
        }
        else
        {
            $sales = sales::with('customer', 'orderbooker', 'supplyman')->whereBetween('date', [$from, $to])->where('branchID', $branch)->get();
            $branch = branches::find($branch);
            $branch = $branch->name;
        }

        

        return view('reports.sales.details', compact('from', 'to', 'sales', 'branch'));
    }
}
