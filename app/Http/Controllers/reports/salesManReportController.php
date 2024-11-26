<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\sales;
use App\Models\salesman;
use Illuminate\Http\Request;

class salesManReportController extends Controller
{
    public function index()
    {
        $salesmans = salesman::all();

        return view('reports.salesman.index', compact('salesmans'));
    }

    public function data($id, $from, $to)
    {

        $sales = sales::with('payments', 'customer')->where('salesmanID', $id)->whereBetween('date', [$from, $to])->get();
        $salesman = salesman::find($id);
        return view('reports.salesman.details', compact('sales', 'from', 'to', 'salesman'));
    }
}
