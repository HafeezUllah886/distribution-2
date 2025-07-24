<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\method_transactions;
use App\Models\sale_payments;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'paymentID' => $payment->id,
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

    public function destroy(Request $request)
    {
        $payment = sale_payments::find($request->id);
        if($payment->userID != $request->user()->id)
        {
            return response()->json([
                'status' => 'Unauthorised',
                'data' => [
                    'message' => 'You are not authorized to delete this payment',
                ]
            ], 401); 
        }
        try
        {
            DB::beginTransaction();
            sale_payments::where('refID', $request->refID)->delete();
            transactions::where('refID', $request->refID)->delete();
            currency_transactions::where('refID', $request->refID)->delete();
            users_transactions::where('refID', $request->refID)->delete();
            method_transactions::where('refID', $request->refID)->delete();
            cheques::where('refID', $request->refID)->delete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'Payment deleted successfully',
                ]
            ], 200);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage(),
                ]
            ], 500);
        }
    }
}
