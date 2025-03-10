<?php

namespace App\Http\Controllers;

use App\Models\currencymgmt;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\User;
use Illuminate\Http\Request;

class CurrencymgmtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = currencymgmt::all();

        return view('Finance.currencymgmt.index', compact('currencies'));
    }

    public function show($id,$user, $from, $to)
    {
        $currency = currencymgmt::find($id);
        $user = User::find($user);

        $transactions = currency_transactions::where(['currencyID' => $id, 'user_id' => $user->id])->whereBetween('date', [$from, $to])->get();

        $pre_cr = currency_transactions::where(['currencyID' => $id, 'user_id' => $user->id])->whereDate('date', '<', $from)->sum('cr');
        $pre_db = currency_transactions::where(['currencyID' => $id, 'user_id' => $user->id])->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = currency_transactions::where(['currencyID' => $id, 'user_id' => $user->id])->sum('cr');
        $cur_db = currency_transactions::where(['currencyID' => $id, 'user_id' => $user->id])->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('Finance.currencymgmt.statment', compact('currency', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to'));
    }



    public function details($userID)
    {
        $user = User::find($userID);
        $currencies = currencymgmt::all();

        return view('Finance.currencymgmt.details', compact('user', 'currencies'));
    }
}
