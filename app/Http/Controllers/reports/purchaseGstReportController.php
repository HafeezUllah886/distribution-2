<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\purchase;
use Illuminate\Http\Request;

class purchaseGstReportController extends Controller
{
    public function index()
    {
        return view('reports.purchaseGst.index');
    }

    public function data($from, $to)
    {
        $purchases = purchase::with('vendor', 'details')->whereBetween('date', [$from, $to])->get();

        foreach($purchases as $purchase)
        {
            $totalRP = 0;
            foreach($purchase->details as $product)
            {
                $totalRP += ($product->qty + $product->bonus) * $product->tp;
            }
            $purchase->totalBill = $totalRP;
        }
            return view('reports.purchaseGst.details', compact('from', 'to', 'purchases'));
    }
}
