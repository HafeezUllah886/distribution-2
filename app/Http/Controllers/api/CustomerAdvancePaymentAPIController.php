<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\CustomerAdvancePayment;
use App\Models\transactions_que;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerAdvancePaymentAPIController extends Controller
{
    public function store(Request $request)
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

        $check = CustomerAdvancePayment::where('key', $request->key)->count();

        if($check > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment already punched'
            ], 201);
        }


        try{ 
            DB::beginTransaction();
            $ref = getRef();
            $payment = CustomerAdvancePayment::create(
                [
                    'customerID'       => $request->customerID,
                    'orderbookerID'    => $request->user()->id,
                    'date'              => $request->date,
                    'amount'            => $request->amount,
                    'method'            => $request->method,
                    'number'            => $request->number,
                    'bank'              => $request->bank,
                    'cheque_date'       => $request->cheque_date,
                    'branchID'         => $request->user()->branchID,
                    'notes'             => $request->notes,
                    'refID'             => $ref,
                    'key'               => $request->key
                ]
            );
            $depositer = accounts::find($request->customerID);
            $user_name = $request->user()->name;

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
                            'date' => $request->date,
                            'notes' => "Mobile - Advance Payment deposited to $user_name : $request->notes",
                            'notes2' => "Mobile - Advance Payment deposited by $depositer->title : $request->notes",
                            'refID' => $ref,
                        ]
                    );
                }
           
            createTransaction($request->customerID, $request->date, 0, $request->amount, "Mobile - Advance Payment deposited to $user_name : $request->notes", $ref, $request->user()->id);
            
            createMethodTransaction($request->user()->id,$request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, "Mobile - Advance Payment deposited by $depositer->title : $request->notes", $ref);
    
            createUserTransaction($request->user()->id, $request->date, $request->amount, 0, "Mobile - Advance Payment deposited by $depositer->title : $request->notes", $ref);

            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'message' => 'Advance Payment received successfully',
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
}
