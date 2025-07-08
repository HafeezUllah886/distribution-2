<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\bulk_payments;
use App\Models\customerPayments;
use App\Models\orderbooker_customers;
use App\Models\orderbookerPaymentsReceiving;
use App\Models\orders;
use App\Models\paymentReceiving;
use App\Models\paymentsReceiving;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\transactions_que;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class customerPaymentsReceivingContoller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function paymentReceiving(request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerID' => 'required|exists:accounts,id',
            'date' => 'required',
            'method' => 'required',
            'amount' => 'required',
            'file' => 'nullable|file|mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        try{ 
            DB::beginTransaction();
            $ref = getRef();
            $payment = paymentsReceiving::create(
                [
                    'depositerID'      => $request->customerID,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'cheque_date'   => $request->cheque_date,
                    'branchID'      => $request->user()->branchID,
                    'notes'         => $request->notes,
                    'userID'        => $request->user()->id,
                    'refID'         => $ref,
                ]
            );

            if($request->method != 'Cash')
                {
                    transactions_que::create(
                        [
                            'userID' => $request->user()->id,
                            'customerID' => $request->customerID,
                            'orderbookerID' => $request->user()->id,
                            'branchID' => $request->user()->branchID,
                            'method' => $request->method,
                            'number' => $request->number,
                            'bank' => $request->bank,
                            'cheque_date' => $request->cheque_date,
                            'amount' => $request->amount,
                            'refID' => $ref,
                        ]
                    );
                }
            $depositer = accounts::find($request->customerID);
            $user_name = $request->user()->name;
            createTransaction($request->customerID, $request->date, 0, $request->amount, "Mobile - Payment deposited to $user_name : $request->notes", $ref, $request->user()->id);
            
            createMethodTransaction($request->user()->id,$request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, "Mobile - Payment deposited by $depositer->title : $request->notes", $ref);
    
            createUserTransaction($request->user()->id, $request->date, $request->amount, 0, "Mobile - Payment deposited by $depositer->title : $request->notes", $ref);

            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'Payment received successfully',
                    'payment' => $payment,
                ]
            ], 201);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        } 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function pendingInvoices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerID' => 'required|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }

        $check = orderbooker_customers::where('orderbookerID', $request->user()->id)->where('customerID', $request->customerID)->first();
        if(!$check)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer does not belong to the orderbooker',
            ], 404);
        }
        
        $invoices = sales::with('payments')->where('customerID', $request->customerID)->where('orderbookerID', $request->user()->id)->unpaidOrPartiallyPaid()->get();
        $data = [];
        foreach($invoices as $invoice)
        {
            $payment = $invoice->payments->sum('amount');
            
            $data[] = [
                'salesID' => $invoice->id,
                'total_bill' => $invoice->net,
                'paid' => $payment,
                'due' => $invoice->net - $payment,
                'date' => $invoice->date,
                'age' => $invoice->age(),
                'payments' => $invoice->payments()->select('method', 'number', 'bank', 'cheque_date', 'amount', 'date', 'notes')->get(),
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function invoicesPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customerID' => 'required|exists:accounts,id',
            'saleIDs' => 'required|array',
            'saleIDs.*' => 'exists:sales,id',
            'amount' => 'required|array',
            'amount.*' => 'numeric|min:0',
            'date' => 'required|date',
            'method' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,png,jpeg',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 422);
        }
        try{
            DB::beginTransaction();
            $data = [];
            $total_amount = 0;
            $ref = getRef();
            foreach($request->saleIDs as $key => $saleID)
            {
                if($request->amount[$key] > 0)
                {
                $total_amount += $request->amount[$key];
                $sale = sales::find($saleID);
                $data[] = sale_payments::create([
                    'salesID' => $saleID,
                    'userID' => $request->user()->id,
                    'orderbookerID' => $request->user()->id,
                    'customerID' => $sale->customerID,
                    'branchID' => $request->user()->branchID,
                    'date' => $request->date,
                    'amount' => $request->amount[$key],
                    'notes' => $request->notes,
                    'method' => $request->method,
                    'bank' => $request->bank,
                    'number' => $request->number,
                    'cheque_date' => $request->cheque_date,
                    'refID' => $ref
                ]);

                $saleIDs[] = $saleID;
                }
            }

            $saleIDs = implode(',', $saleIDs);
            bulk_payments::create([
                'customerID' => $sale->customerID,
                'orderbookerID' => $sale->orderbookerID,
                'date' => $request->date,
                'amount' => $total_amount,
                'notes' => $request->notes,
                'branchID' => $request->user()->branchID,
                'method' => $request->method,
                'bank' => $request->bank,
                'number' => $request->number,
                'cheque_date' => $request->cheque_date,
                'userID' => $request->user()->id,
                'refID' => $ref,
                'invoiceIDs' => $saleIDs
            ]); 

            if($request->method != 'Cash')
                {
                    transactions_que::create(
                        [
                            'userID' => $request->user()->id,
                            'customerID' => $sale->customerID,
                            'orderbookerID' => $sale->orderbookerID,
                            'branchID' => $request->user()->branchID,
                            'method' => $request->method,
                            'number' => $request->number,
                            'bank' => $request->bank,
                            'cheque_date' => $request->cheque_date,
                            'amount' => $request->amount,
                            'refID' => $ref,
                        ]
                    );
                }
            $user = $request->user()->name;
            $customer = accounts::find($sale->customerID);
            createTransaction($request->customerID, $request->date,0, $total_amount, "Mobile - Bulk Payment of Inv No. $saleIDs Received by $user Method: $request->method", $ref, $sale->orderbookerID);
            createUserTransaction($request->user()->id, $request->date,$total_amount, 0, "Mobile - Bulk Payment of Inv No. $saleIDs Received from $customer->title Method: $request->method", $ref);
           createMethodTransaction($request->user()->id, $request->method, $total_amount,0, $request->date, $request->number, $request->bank, $request->cheque_date, "Mobile - Bulk Payment of Inv No. $saleIDs Received from $customer->title Method: $request->method", $ref);
            
            if($request->has('file'))
            {
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 200);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

   public function lastPayment(Request $request)
   {
    $validation = Validator::make($request->all(), [
        'customerID' => 'required|exists:accounts,id',
    ]);

    if($validation->fails())
    {
        return response()->json([
            'status' => 'error',
            'message' => $validation->errors(),
        ], 422);
    }

    $sale_payment_date = sale_payments::where('customerID', $request->customerID)->orderBy('id', 'desc')->pluck('date');
    $payment_receiving_date = paymentsReceiving::where('depositerID', $request->customerID)->orderBy('id', 'desc')->pluck('date');

    //newest date
    $newest_date = $sale_payment_date->max();
    if($payment_receiving_date->max() > $newest_date)
    {
        $newest_date = $payment_receiving_date->max();
    }
    
    $methods = ['Cash', 'Cheque', 'Online', 'Other'];
    $methodData = [];
    $methodData['date'] = $newest_date;
   foreach($methods as $method)
   {
    $sales_payment = sale_payments::where('customerID', $request->customerID)->where('date', $newest_date)->where('method', $method)->sum('amount');
    $payment_receiving = paymentsReceiving::where('depositerID', $request->customerID)->where('date', $newest_date)->where('method', $method)->sum('amount');

    $total = $sales_payment + $payment_receiving;
    $methodData[$method] = round($total, 2);
   }

   $last_sale = sales::where('customerID', $request->customerID)->orderBy('id', 'desc')->first()->date;
   $last_sale_amount = sales::where('customerID', $request->customerID)->orderBy('id', 'desc')->first()->net;
   $last_balance = getAccountBalanceOrderbookerWise($request->customerID, $request->user()->id);

   $methodData['last_sale'] = $last_sale;
   $methodData['last_sale_amount'] = round($last_sale_amount, 2);
   $methodData['last_balance'] = round($last_balance, 2);
  

    return response()->json([
        'status' => 'success',
        'data' => $methodData
    ], 200);
   }
}
