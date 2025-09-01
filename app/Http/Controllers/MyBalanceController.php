<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\User;
use App\Models\users_transactions;
use Illuminate\Http\Request;

class MyBalanceController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
       
       $currencies = currencymgmt::all();
       foreach ($currencies as $currency) {
           $currency->opening_balance = currency_transactions::where('currencyID', $currency->id)->where('userID', auth()->user()->id)->whereDate('date', '<', $start)->sum('cr') - currency_transactions::where('currencyID', $currency->id)->where('userID', auth()->user()->id)->whereDate('date', '<', $start)->sum('db');
           $currency->credit = currency_transactions::where('currencyID', $currency->id)->where('userID', auth()->user()->id)->whereBetween('date', [$start, $end])->sum('cr');
           $currency->debit = currency_transactions::where('currencyID', $currency->id)->where('userID', auth()->user()->id)->whereBetween('date', [$start, $end])->sum('db');
           $currency->balance = getCurrencyBalance($currency->id, auth()->user()->id);
       }

       
       $mainAccountOpeningBalance = users_transactions::where('userID', auth()->user()->id)->whereDate('date', '<', $start)->sum('cr') - users_transactions::where('userID', auth()->user()->id)->whereDate('date', '<', $start)->sum('db');
       $mainAccountCredit = users_transactions::where('userID', auth()->user()->id)->whereBetween('date', [$start, $end])->sum('cr');
       $mainAccountDebit = users_transactions::where('userID', auth()->user()->id)->whereBetween('date', [$start, $end])->sum('db');
       $mainAccountBalanceTillDate = $mainAccountOpeningBalance + $mainAccountCredit - $mainAccountDebit;
       $mainAccountBalance = getUserAccountBalance(auth()->user()->id);

$methods = ["Cash", "Online", "Cheque", "Other"];
$methodData = [];

foreach ($methods as $method) {
    $openingBalance = method_transactions::where('method', $method)
        ->where('userID', auth()->id())
        ->whereDate('date', '<', $start)
        ->sum(\DB::raw('COALESCE(cr, 0) - COALESCE(db, 0)'));

    $credit = method_transactions::where('method', $method)
        ->where('userID', auth()->id())
        ->whereBetween('date', [$start, $end])
        ->sum('cr') ?? 0;

    $debit = method_transactions::where('method', $method)
        ->where('userID', auth()->id())
        ->whereBetween('date', [$start, $end])
        ->sum('db') ?? 0;

        $balancetill = $openingBalance + $credit - $debit;

    $balance = getMethodBalance($method, auth()->id());

    $methodData[] = [
        'name' => $method,
        'opening_balance' => $openingBalance,
        'credit' => $credit,
        'debit' => $debit,
        'balance' => $balance,
        'balance_till_date' => $balancetill,
    ];
}

// Now $methodData contains all the calculated values
       return view('Finance.my_balance.index', compact('currencies', 'start', 'end', 'mainAccountBalance', 'mainAccountOpeningBalance', 'mainAccountCredit', 'mainAccountDebit', 'mainAccountBalanceTillDate', 'methodData'));
    }

    public function staff_balance(Request $request, $staff)
    {
        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? lastDayOfMonth();
        $staff = User::find($staff);
       
       $currencies = currencymgmt::all();
       foreach ($currencies as $currency) {
           $currency->opening_balance = currency_transactions::where('currencyID', $currency->id)->where('userID', $staff->id)->whereDate('date', '<', $start)->sum('cr') - currency_transactions::where('currencyID', $currency->id)->where('userID', $staff->id)->whereDate('date', '<', $start)->sum('db');
           $currency->credit = currency_transactions::where('currencyID', $currency->id)->where('userID', $staff->id)->whereBetween('date', [$start, $end])->sum('cr');
           $currency->debit = currency_transactions::where('currencyID', $currency->id)->where('userID', $staff->id)->whereBetween('date', [$start, $end])->sum('db');
           $currency->balance = getCurrencyBalance($currency->id, $staff->id);
       }

       
       $mainAccountOpeningBalance = users_transactions::where('userID', $staff->id)->whereDate('date', '<', $start)->sum('cr') - users_transactions::where('userID', $staff->id)->whereDate('date', '<', $start)->sum('db');
       $mainAccountCredit = users_transactions::where('userID', $staff->id)->whereBetween('date', [$start, $end])->sum('cr');
       $mainAccountDebit = users_transactions::where('userID', $staff->id)->whereBetween('date', [$start, $end])->sum('db');
       $mainAccountBalanceTillDate = $mainAccountOpeningBalance + $mainAccountCredit - $mainAccountDebit;
       $mainAccountBalance = getUserAccountBalance($staff->id);

$methods = ["Cash", "Online", "Cheque", "Other"];
$methodData = [];

foreach ($methods as $method) {
    $openingBalance = method_transactions::where('method', $method)
        ->where('userID', $staff->id)
        ->whereDate('date', '<', $start)
        ->sum(\DB::raw('COALESCE(cr, 0) - COALESCE(db, 0)'));

    $credit = method_transactions::where('method', $method)
        ->where('userID', $staff->id)
        ->whereBetween('date', [$start, $end])
        ->sum('cr') ?? 0;

    $debit = method_transactions::where('method', $method)
        ->where('userID', $staff->id)
        ->whereBetween('date', [$start, $end])
        ->sum('db') ?? 0;

        $balancetill = $openingBalance + $credit - $debit;

    $balance = getMethodBalance($method, $staff->id);

    $methodData[] = [
        'name' => $method,
        'opening_balance' => $openingBalance,
        'credit' => $credit,
        'debit' => $debit,
        'balance' => $balance,
        'balance_till_date' => $balancetill,
    ];
}

// Now $methodData contains all the calculated values
       return view('Finance.staff_balance.details', compact('currencies', 'start', 'end', 'mainAccountBalance', 'mainAccountOpeningBalance', 'mainAccountCredit', 'mainAccountDebit', 'mainAccountBalanceTillDate', 'methodData', 'staff'));
    }
}
