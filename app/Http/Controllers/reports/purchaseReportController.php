<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
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
            $vendors = accounts::vendor()->get();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
            $vendors = accounts::vendor()->where('branchID', auth()->user()->branchID)->get();
        }
        return view('reports.purchases.index', compact('branches', 'vendors'));
    }

    public function data(request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branch = $request->branch;
        if($branch == "All")
        {
            $purchases = purchase::with('vendor', 'details', 'branch')->whereBetween('orderdate', [$from, $to]);
            if($request->vendor)
            {
                $purchases = $purchases->whereIn('vendorID', $request->vendor);
            }
            $purchases = $purchases->get();
        }
        else
        {
            $purchases = purchase::with('vendor', 'details', 'branch')->whereBetween('orderdate', [$from, $to])->where('branchID', $branch);
            if($request->vendor)
            {
                $purchases = $purchases->whereIn('vendorID', $request->vendor);
            }
            $purchases = $purchases->get();
            $branch = branches::find($branch);
            $branch = $branch->name;
        }

        return view('reports.purchases.details', compact('from', 'to', 'purchases', 'branch'));
    }
}
