<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\sales;
use App\Models\User;
use Illuminate\Http\Request;

class OrderbookerWiseCustomerBalanceReport extends Controller
{
    public function index(Request $request)
    {
       
       $customers = accounts::customer()->currentBranch()->get();
       
        return view('reports.orderbooker_customer_balance.index', compact('customers'));
    }

    public function data(Request $request)
    {

        $customer = $request->customer;
        $orderbooker = $request->orderbooker;
        $sales = sales::with('customer', 'orderbooker', 'payments')->where('customerID', $customer)->where('orderbookerID', $orderbooker)
        ->UnpaidOrPartiallyPaid()
        ->get();

        $customer = accounts::find($customer);
        $orderbooker = User::find($orderbooker);


        return view('reports.orderbooker_customer_balance.details', compact('sales', 'customer', 'orderbooker'));
    }

    public function getOrderbookersByCustomer($customer)
    {
        $orderbookerIDs = orderbooker_customers::where('customerID', $customer)->pluck('orderbookerID')->toArray();

        $orderbookers = User::whereIn('id', $orderbookerIDs)->active()->select('id as value', 'name as text')->get();

        return response()->json($orderbookers);

    }
}
