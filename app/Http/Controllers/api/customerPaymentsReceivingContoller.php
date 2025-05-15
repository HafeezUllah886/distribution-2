<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\customerPayments;
use App\Models\orderbooker_customers;
use App\Models\orderbookerPaymentsReceiving;
use App\Models\sale_payments;
use App\Models\sales;
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
            'amount' => 'required',
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
            $payment = customerPayments::create(
                [
                    'customerID'    => $request->customerID,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'branchID'      => $request->user()->branchID,
                    'notes'         => $request->notes,
                    'receivedBy'    => $request->user()->id,
                    'refID'         => $ref,
                ]
            );
            $customer = accounts::find($request->customerID);
            $user_name = request()->user()->name;
            createTransaction($request->customerID, $request->date,0, $request->amount, "Payment submitted to $user_name", $ref);
            createUserTransaction($request->user()->id, $request->date,$request->amount, 0, "Payment received from customer: $customer->title", $ref);
           
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Payment received successfully',
                'data' => [
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
                'due' => $invoice->net - $payment
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
            'payment_method' => 'required|string',
            'cheque_no' => 'required_if:payment_method,cheque|string',
            'cheque_date' => 'required_if:payment_method,cheque|date',
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
            foreach($request->saleIDs as $key => $saleID)
            {
                $total_amount += $request->amount[$key];
                $ref = getRef();
                $sale = sales::find($saleID);
                $data[] = sale_payments::create([
                    'salesID' => $saleID,
                    'date' => $request->date,
                    'amount' => $request->amount[$key],
                    'notes' => $request->notes,
                    'userID' => $request->user()->id,
                    'refID' => $ref
                ]);

                createTransaction($sale->customerID, $request->date,0, $request->amount[$key], "Payment of Inv No. $sale->id", $ref);
                createUserTransaction(auth()->id(), $request->date,$request->amount[$key], 0, "Payment of Inv No. $sale->id", $ref);
            }

            orderbookerPaymentsReceiving::create([
                'customerID' => $request->customerID,
                'orderbookerID' => $request->user()->id,
                'receivedBy' => $request->user()->id,
                'date' => $request->date,
                'amount' => $total_amount,
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'cheque_no' => $request->cheque_no,
                'cheque_date' => $request->cheque_date,
                'bank_name' => $request->bank_name,
                'refID' => $ref
            ]);

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
    
    $payment = orderbookerPaymentsReceiving::where('customerID', $request->customerID)->where('orderbookerID', $request->user()->id)->orderBy('id', 'desc')->first();
    return response()->json([
        'status' => 'success',
        'data' => $payment
    ], 200);
   }
}
