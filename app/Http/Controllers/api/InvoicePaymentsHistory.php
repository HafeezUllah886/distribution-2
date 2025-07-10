<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\sale_payments;
use Illuminate\Http\Request;

class InvoicePaymentsHistory extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->start_date ?? firstDayOfMonth();
        $end_date = $request->end_date ?? lastDayOfMonth();

        $payments = sale_payments::whereBetween('date', [$start_date, $end_date])->where('userID', $request->user()->id)->get();

        $data = [];
        foreach($payments as $payment)
        {
            $data[] = [
                'salesID' => $payment->salesID,
                'customer' => $payment->bill->first()->customer->title,
                'method' => $payment->method,
                'number' => $payment->number,
                'bank' => $payment->bank,
                'cheque_date' => $payment->cheque_date,
                'amount' => $payment->amount,
                'date' => $payment->date,
                'notes' => $payment->notes,
                'refID' => $payment->refID,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Payments retrieved successfully',
                'data' => [
                    'payments' => $data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,  
                      
                ],
            ]
        ], 200);
    }
}
