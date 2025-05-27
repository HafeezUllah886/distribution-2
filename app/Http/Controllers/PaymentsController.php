<?php

namespace App\Http\Controllers;

use App\Models\vendorPayments;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\payments;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = payments::currentBranch()->orderBy('id', 'desc')->get();
        $receivers = accounts::whereIn('type', ['Business', 'Vendor', 'Supply Man', 'Unloader'])->currentBranch()->active()->get();
        $currencies = currencymgmt::all();
        return view('Finance.payments.index', compact('payments', 'receivers', 'currencies'));
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
            $ref = getRef();
            payments::create(
                [
                    'receiverID'      => $request->receiverID,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'remarks'       => $request->remarks,
                    'branchID'      => auth()->user()->branchID,
                    'notes'         => $request->notes,
                    'userID'        => auth()->user()->id,
                    'refID'         => $ref,
                ]
            );
            $receiver = accounts::find($request->receiverID);
            $user_name = auth()->user()->name;
            createMethodTransaction($request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->remarks, $request->notes, $ref);
            createTransaction($request->receiverID, $request->date, $request->amount, 0, "Payment by $user_name", $ref);
            createUserTransaction(auth()->user()->id, $request->date,0, $request->amount, "Payment to $receiver->title", $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, "Payment to $receiver->title", $ref);
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
        $payment = payments::find($id);
        return view('Finance.payments.receipt', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(vendorPayments $vendorPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, vendorPayments $vendorPayments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($ref)
    {
        try
        {
            DB::beginTransaction();
            vendorPayments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('vendor_payments.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('vendor_payments.index')->with('error', $e->getMessage());
        }
    }
}
