<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\orderbooker_customers;
use Illuminate\Http\Request;

class CustomerBalanceReporController extends Controller
{
    public function index(Request $request)
    {
        $customer_id = orderbooker_customers::where('orderbookerID', $request->user()->id)->pluck('customerID')->toArray();
        $customers = accounts::whereIn('id', $customer_id)->active()->get();
        foreach ($customers as $customer) {
            $balance = getAccountBalanceOrderbookerWise($customer->id, $request->user()->id);
            $customer->balance = $balance;
            $customer->area_name = area::find($customer->areaID)->name;

        }

        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ], 200);
    }
}
