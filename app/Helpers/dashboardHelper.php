<?php

use App\Models\accounts;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\sale_details;

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
    $file2 = filesize(public_path('assets/images/header.jpeg'));

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
