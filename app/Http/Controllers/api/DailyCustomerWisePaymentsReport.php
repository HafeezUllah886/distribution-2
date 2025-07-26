<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\paymentsReceiving;
use App\Models\sale_payments;
use Illuminate\Http\Request;

class DailyCustomerWisePaymentsReport extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? date('Y-m-d');
        $to = $request->to ?? date('Y-m-d');
        
        $customers = orderbooker_customers::where('orderbookerID', $request->user()->id)->pluck('customerID')->toArray();

        $methods = ['Cash', 'Cheque', 'Online', 'Other'];
        $methodData = [];
        foreach($customers as $customer)
        {
            $customerTitle = accounts::find($customer)->title;
            $sales_payment = sale_payments::where('customerID', $customer)->whereBetween('date', [$from, $to])->where('userID', $request->user()->id)->sum('amount');
            $payment_receiving = paymentsReceiving::where('depositerID', $customer)->whereBetween('date', [$from, $to])->where('userID', $request->user()->id)->sum('amount');

            $total = $sales_payment + $payment_receiving;
            if($total > 0)
            {
                foreach($methods as $method)
                {
                    $sales_payment = sale_payments::where('customerID', $customer)->whereBetween('date', [$from, $to])->where('userID', $request->user()->id)->where('method', $method)->sum('amount');
                    $payment_receiving = paymentsReceiving::where('depositerID', $customer)->whereBetween('date', [$from, $to])->where('userID', $request->user()->id)->where('method', $method)->sum('amount');
                    $methodData[$customerTitle][$method] = $sales_payment + $payment_receiving;
                }
            }
           
        }

        $totalMethodData = [];
        foreach($methods as $method)
        {
            $totalMethodData[$method] = 0;
            foreach($methodData as $customer)
            {
                $totalMethodData[$method] += $customer[$method];
            }
        }
       
        return response()->json([
            'status' => 'success',
            'data' => compact('methodData', 'totalMethodData') 
        ], 200);
    }
}
