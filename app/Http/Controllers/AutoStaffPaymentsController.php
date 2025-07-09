<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\payments;
use App\Models\staffPayments;
use App\Models\transactions;
use App\Models\transactions_que;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutoStaffPaymentsController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('role', ['Operator', 'Order Booker'])->currentBranch()->get();

        return view('Finance.auto_staff_payments.index', compact('staff'));
    }

    public function create(Request $request)
    {
        $staff = $request->staff;
        $method = $request->method;
       
        $transactions = transactions_que::where('userID', $staff)->where('method', $method)->where('status', 'pending')->get();

        if($transactions->isEmpty())
        {
            return redirect()->back()->with('error', 'No transactions found');
        }

        $accounts = accounts::where('type', $request->forward)->currentBranch()->get();

        $staff = User::find($staff);

        return view('Finance.auto_staff_payments.create', compact('transactions', 'staff', 'method', 'accounts'));
    }

    public function store(Request $request)
    {
       try{
        DB::beginTransaction();
        $transactions = $request->transactions;
        $account = $request->account;
        $notes = $request->notes;

        foreach($transactions as $key => $transaction)
        {
            $que = transactions_que::find($transaction);

            $ref = getRef();
            staffPayments::create(
                [
                    'fromID'        => $que->userID,
                    'date'          => now(),
                    'amount'        => $que->amount,
                    'method'        => $que->method,
                    'number'        => $que->number,
                    'bank'          => $que->bank,
                    'cheque_date'   => $que->cheque_date,
                    'notes'         => $que->notes,
                    'receivedBy'    => auth()->id(),
                    'refID'         => $ref,
                ]
            );
            
            $user_name = auth()->user()->name;
            $staff = User::find($que->userID);
            $notes = $que->notes;
            $notes1 = "Payment submitted to $user_name Method $que->method Notes : $request->notes";

            createUserTransaction(auth()->id(), now(),$que->amount, 0, $notes, $ref);
            createUserTransaction($que->userID, now(),0, $que->amount, $notes1, $ref);
           
            createMethodTransaction($que->userID,$que->method, 0, $que->amount, now(), $que->number, $que->bank, $que->cheque_date, $notes1, $ref);
            createMethodTransaction(auth()->user()->id,$que->method, $que->amount, 0, now(), $que->number, $que->bank, $que->cheque_date, $notes, $ref);

            if($que->method == 'Cheque')
            {
                saveCheque($que->customerID, auth()->user()->id, $que->orderbookerID, $que->cheque_date, $que->amount, $que->number, $que->bank, $notes, $ref);
            }

            $que->update([
                'status' => 'completed',
                'trefID' => $ref
            ]);

            if($request->account != null)
            {
                payments::create(
                    [
                        'receiverID'      => $request->account,
                        'date'          => now(),
                        'amount'        => $que->amount,
                        'method'        => $que->method,
                        'number'        => $que->number,
                        'bank'          => $que->bank,
                        'cheque_date'   => $que->cheque_date,
                        'branchID'      => auth()->user()->branchID,
                        'notes'         => $request->notes,
                        'userID'        => auth()->user()->id,
                        'refID'         => $ref,
                    ]
                );
                $receiver = accounts::find($request->account);
                $user_name = auth()->user()->name;
                $notes = "Payment to $receiver->title Method $que->method Notes : $request->notes";
    
                createTransaction($request->account, now(), $que->amount, 0, $notes, $ref, $que->orderbookerID);
                createMethodTransaction(auth()->user()->id,$que->method, 0, $que->amount, now(), $que->number, $que->bank, $que->cheque_date, $notes, $ref);
               
                createUserTransaction(auth()->user()->id, now(),0, $que->amount, $notes, $ref);
            }
           
        }
        DB::commit();
        return to_route('auto_staff_payments')->with('success', "Auto Staff Payments Created");
       }
       catch(\Exception $e)
       {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
       }
    }
}
