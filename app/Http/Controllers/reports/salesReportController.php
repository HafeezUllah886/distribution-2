<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\sales;
use Illuminate\Http\Request;

class salesReportController extends Controller
{
    public function index()
    {
        return view('reports.sales.index');
    }

    public function data($from, $to, $type)
    {
        if($type == "All")
        {
            $sales = sales::with('customer', 'details')->whereBetween('date', [$from, $to])->get();
        }
        else
        {
            $customers = accounts::where('type', 'Customer')->where('c_type', $type)->pluck('id')->toArray();
            $sales = sales::with('customer', 'details')->whereIn('customerID', $customers)->whereBetween('date', [$from, $to])->get();
        }


        return view('reports.sales.details', compact('from', 'to', 'sales'));
    }
}
