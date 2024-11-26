<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\purchase;
use Illuminate\Http\Request;

class purchaseReportController extends Controller
{
    public function index()
    {
        return view('reports.purchases.index');
    }

    public function data($from, $to)
    {
        $purchases = purchase::with('vendor', 'details')->whereBetween('date', [$from, $to])->get();

        return view('reports.purchases.details', compact('from', 'to', 'purchases'));
    }
}
