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
        $forward = $request->forward ?? 'Vendor';
       
        $transactions = transactions_que::where('userID', $staff)->where('method', $method)->where('status', 'pending')->get();

        if($transactions->isEmpty())
        {
            return redirect()->back()->with('error', "No transactions found $staff, $method, $forward");
        }

        $accounts = accounts::where('type', $forward)->currentBranch()->get();

        $staff = User::find($staff);

        return view('Finance.auto_staff_payments.create', compact('transactions', 'staff', 'method', 'accounts', 'forward'));
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

            if($que->method == 'Online')
            {
                $date = $que->date;
            }
            else
            {
                $date = now();
            }

            $ref = $que->refID;
            staffPayments::create(
                [
                    'fromID'        => $que->userID,
                    'date'          => $date,
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
            $notes = "User: $staff->name - $que->notes";
            $notes1 = "User: $staff->name - $que->notes2";

            createUserTransaction(auth()->id(), $date,$que->amount, 0, $notes, $ref);
            createUserTransaction($que->userID, $date,0, $que->amount, $notes1, $ref);
           
            createMethodTransaction($que->userID,$que->method, 0, $que->amount, $date, $que->number, $que->bank, $que->cheque_date, $notes, $ref);
            createMethodTransaction(auth()->user()->id,$que->method, $que->amount, 0, $date, $que->number, $que->bank, $que->cheque_date, $notes1, $ref);

            if($que->method == 'Cheque')
            {
                saveCheque($que->customerID, auth()->user()->id, $que->orderbookerID, $que->cheque_date, $que->amount, $que->number, $que->bank, $notes1, $ref);
            }

            $que->update([
                'status' => 'completed',
                'trefID' => $ref
            ]);

            if($request->account != null)
            {
                payments::create(
                    [
                        'receiverID'     => $request->account,
                        'date'          => $date,
                        'amount'        => $que->amount,
                        'method'        => $que->method,
                        'number'        => $que->number,
                        'bank'          => $que->bank,
                        'cheque_date'   => $que->cheque_date,
                        'branchID'      => auth()->user()->branchID,
                        'notes'         => $que->notes2,
                        'userID'        => auth()->user()->id,
                        'refID'         => $ref,
                    ]
                );
                $receiver = accounts::find($request->account);
                $user_name = auth()->user()->name;
               
                createTransaction($request->account, $date, $que->amount, 0, $notes1, $ref, $que->orderbookerID);
                createMethodTransaction(auth()->user()->id,$que->method, 0, $que->amount, $date, $que->number, $que->bank, $que->cheque_date, $notes1, $ref);
               
                createUserTransaction(auth()->user()->id, $date,0, $que->amount, $notes1, $ref);
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
