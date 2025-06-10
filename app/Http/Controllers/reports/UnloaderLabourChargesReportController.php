<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\purchase;
use Illuminate\Http\Request;

class UnloaderLabourChargesReportController extends Controller
{
    public function index()
    {
        $unloader = accounts::where('type', 'Unloader')->currentBranch()->get();
        return view('reports.unloaderReport.index', compact('unloader'));
    }

    public function data(Request $request)
    {
        $unloader = accounts::find($request->unloader);
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? now();

        $purchases = purchase::with('vendor')->where('unloaderID', $unloader->id)->whereBetween('recdate', [$from, $to])->get();
        return view('reports.unloaderReport.details', compact('unloader', 'from', 'to', 'purchases'));
    }
}
