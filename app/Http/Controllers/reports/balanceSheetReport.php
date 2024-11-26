<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\transactions;
use Illuminate\Http\Request;

class balanceSheetReport extends Controller
{
    public function index()
    {
        $accounts = accounts::all();
        return view('reports.balanceSheet.index', compact('accounts'));
    }

    public function data($type, $from, $to)
    {
        $ids = accounts::where('type', $type)->pluck('id')->toArray();

        $transactions = transactions::whereIn('accountID', $ids)->whereBetween('date', [$from, $to])->get();

        $pre_cr = transactions::whereIn('accountID', $ids)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = transactions::whereIn('accountID', $ids)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = transactions::whereIn('accountID', $ids)->sum('cr');
        $cur_db = transactions::whereIn('accountID', $ids)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('reports.balanceSheet.details', compact('type', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to'));
    }
}
