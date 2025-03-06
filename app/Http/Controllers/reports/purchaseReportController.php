<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\purchase;
use Illuminate\Http\Request;

class purchaseReportController extends Controller
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
        return view('reports.purchases.index', compact('branches'));
    }

    public function data($from, $to, $branch)
    {
        if($branch == "All")
        {
            $purchases = purchase::with('vendor', 'details', 'branch')->whereBetween('orderdate', [$from, $to])->get();
        }
        else
        {
            $purchases = purchase::with('vendor', 'details', 'branch')->whereBetween('orderdate', [$from, $to])->where('branchID', $branch)->get();
            $branch = branches::find($branch);
            $branch = $branch->name;
        }

        return view('reports.purchases.details', compact('from', 'to', 'purchases', 'branch'));
    }
}
