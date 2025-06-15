<?php

namespace App\Http\Controllers;

use App\Models\staffPayments;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\User;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receivings = staffPayments::where('receivedBy', auth()->user()->id)->orderBy('id', 'desc')->get();
        $users = User::where('branchID', auth()->user()->branchID)->where('id', '!=', auth()->user()->id)->get();
        $currencies = currencymgmt::all();
        $customers = accounts::customer()->currentBranch()->active()->get();
        return view('Finance.staff_payments.index', compact('receivings', 'users', 'currencies', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       try{ 
            DB::beginTransaction(); 
            $staff = User::find($request->fromID);
            if(!checkMethodExceed($request->method,$request->fromID, $request->amount))
            {
             throw new \Exception("Method Amount Exceed");
            }
            if(!checkUserAccountExceed($request->fromID, $request->amount))
            {
             throw new \Exception("User Account Amount Exceed");
            }
           if($request->method == 'Cash')
           {
            if($staff->role != 'Order Booker')
                if(!checkCurrencyExceed($request->fromID, $request->currencyID, $request->qty))
                {
                    throw new \Exception("Currency Qty Exceed");
                }
           }
            $ref = getRef();
            staffPayments::create(
                [
                    'fromID'        => $request->fromID,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'cheque_date'   => $request->cheque_date,
                    'notes'         => $request->notes,
                    'receivedBy'    => auth()->id(),
                    'refID'         => $ref,
                ]
            );
            
            $user_name = auth()->user()->name;
            $notes = "Payment received from staff: $staff->name Method $request->method Notes : $request->notes";
            $notes1 = "Payment submitted to $user_name Method $request->method Notes : $request->notes";

            createUserTransaction(auth()->id(), $request->date,$request->amount, 0, $notes, $ref);
            createUserTransaction($request->fromID, $request->date,0, $request->amount, $notes1, $ref);
           
            createMethodTransaction($staff->id,$request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, $notes1, $ref);
            createMethodTransaction(auth()->user()->id,$request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, $notes, $ref);
                createCurrencyTransaction($request->fromID, $request->currencyID, $request->qty, 'db', $request->date, $notes1, $ref);
            }
            if($request->method == 'Cheque')
            {
                saveCheque($request->customerID, auth()->user()->id, $request->cheque_date, $request->amount, $request->number, $request->bank, $request->notes, $ref);
            }
            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();
            return back()->with('success', "Payment Saved");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }  
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receiving = staffPayments::find($id);
        return view('Finance.staff_payments.receipt', compact('receiving'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(staffPayments $staffPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, staffPayments $staffPayments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        try{
            DB::beginTransaction();
            staffPayments::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('staff_payments.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('staff_payments.index')->with('error', $e->getMessage());
        }
    }
}
