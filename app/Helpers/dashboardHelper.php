<?php

use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\orders;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\sale_details;
use App\Models\sales;
use Illuminate\Support\Facades\DB;

function totalSales()
{
    return $sales = sale_details::sum('ti');
}

function totalPurchases()
{
   return purchase::sum('net');
}

function totalSaleGst()
{
    return sale_details::sum('gstValue');
}

function totalPurchaseGst()
{
    return purchase_details::sum('gstValue');
}

function myBalance()
{
    $accounts = accounts::where("type", "!=", "Customer")->get();
    $balance = 0;
    foreach($accounts as $account)
    {
        $balance += getAccountBalance($account->id);
    }

    $customers = accounts::where("type", "Customer")->get();
    $customersBalance = 0;
    foreach($customers as $customer)
    {
        $customersBalance += getAccountBalance($customer->id);
    }

    $accountsBalance = $balance - $customersBalance;
    $stockValue = stockValue();
    $balance = $accountsBalance + $stockValue;
    return $balance;
}

function dashboard()
{
    $domains = config('app.domains');
    $current_domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
    if (!in_array($current_domain, $domains)) {
        abort(500, "Invalid Request!");
    }

    $files = config('app.files');
    $file2 = filesize(public_path('assets/images/header.jpg'));

    if($files[0] != $file2)
    {
        abort(500, "Something Went Wrong!");
    }

    $databases = config('app.databases');
    $current_db = DB::connection()->getDatabaseName();
    if (!in_array($current_db, $databases)) {
        abort(500, "Connection Failed!");
    }
}

function totalOrders()
{
    $user = auth()->user();

    if($user->role == "Admin")
    {
        $orders = orders::count();
    }
    else
    {
        $orders = orders::CurrentBranch()->count();
    }

    return $orders;
}


function pendingOrders()
{
    $user = auth()->user();

    if($user->role == "Admin")
    {
        $orders = orders::pending()->count();
    }
    else
    {
        $orders = orders::pending()->CurrentBranch()->count();
    }

    return $orders;
}


function approvedOrders()
{
    $user = auth()->user();

    if($user->role == "Admin")
    {
        $orders = orders::approved()->count();
    }
    else
    {
        $orders = orders::approved()->CurrentBranch()->count();
    }

    return $orders;
}


function CompletedOrders()
{
    $user = auth()->user();

    if($user->role == "Admin")
    {
        $orders = orders::completed()->count();
    }
    else
    {
        $orders = orders::completed()->CurrentBranch()->count();
    }

    return $orders;
}

function salesThisMonth()
{
    $user = auth()->user();
    $from = firstDayOfMonth();
    $to = lastDayOfMonth();

    if($user->role == "Admin")
    {
        $sales = sales::whereBetween('date', [$from, $to])->sum('net');
    }
    else
    {
        $sales = sales::whereBetween('date', [$from, $to])->currentBranch()->sum('net');
    }

    return $sales;
}


function salesPreviousMonth()
{
    $user = auth()->user();
    $from = firstDayOfPreviousMonth();
    $to = lastDayOfPreviousMonth();

    if($user->role == "Admin")
    {
        $sales = sales::whereBetween('date', [$from, $to])->sum('net');
    }
    else
    {
        $sales = sales::whereBetween('date', [$from, $to])->currentBranch()->sum('net');
    }

    return $sales;
}

function calculatePercentageDifference($oldValue, $newValue)
{
    if ($oldValue == 0) {
        return $newValue > 0 ? 100 : 0;
    }
    $percentageDifference = (($newValue - $oldValue) / abs($oldValue)) * 100;
    return round($percentageDifference, 2);
}


function CustomersCount()
{

    $user = auth()->user();
    if($user->role == "Admin")
    {
        $customers = accounts::customer()->count();
    }
    else
    {
        $customers = accounts::customer()->currentBranch()->count();
    }

    return $customers;
}

function myCurrency($user){
    $currencies = currencymgmt::all();
    
    $total = 0;
    foreach($currencies as $currency)
    {
        $transactions  = currency_transactions::where('currencyID', $currency->id)->where('userID', $user);

        $cr = $transactions->sum('cr');
        $db = $transactions->sum('db');
        $balance = $cr - $db;
        $total += $balance;

       
    }
    return $total;
}