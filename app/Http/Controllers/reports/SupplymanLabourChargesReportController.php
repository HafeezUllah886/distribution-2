<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\sales;
use Illuminate\Http\Request;

class SupplymanLabourChargesReportController extends Controller
{
    public function index()
    {
        $supplymens = accounts::where('type', 'Supply Man')->currentBranch()->get();
        return view('reports.supplymanReport.index', compact('supplymens'));
    }

    public function data(Request $request)
    {
        $supplyman = accounts::find($request->supplyman);
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? now();

        $sales = sales::with('customer', 'orderbooker', 'details')->where('supplymanID', $supplyman->id)->whereBetween('date', [$from, $to])->get();
        return view('reports.supplymanReport.details', compact('supplyman', 'from', 'to', 'sales'));
    }
}
