<?php

namespace App\Http\Controllers;

use App\Models\labor_payments;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaborPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = labor_payments::currentBranch()->orderBy('id', 'desc')->get();
        $labor = accounts::labor()->get();
        $currencies = currencymgmt::all();
        return view('Finance.labor_payments.index', compact('payments', 'labor', 'currencies'));
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
            labor_payments::create(
                [
                    'laborID'      => $request->laborID,
                    'date'          => $request->date,
                    'amount'        => $request->total,
                    'branchID'      => auth()->user()->branchID,
                    'notes'         => $request->notes,
                    'userID'        => auth()->id(),
                    'refID'         => $ref,
                ]
            );
            $labor = accounts::find($request->laborID);
            $user_name = auth()->user()->name;
            createTransaction($request->laborID, $request->date,$request->total, 0, "Payment paid by $user_name", $ref);
            createUserTransaction(auth()->id(), $request->date,0, $request->total, "Payment paid to labor: $labor->title", $ref);
            createCurrencyTransaction(auth()->id(), $request->currencyID, $request->qty, 'db', $request->date, "Payment paid to labor: $labor->title", $ref);
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
    public function show(labor_payments $labor_payments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(labor_payments $labor_payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, labor_payments $labor_payments)
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
            labor_payments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('labor_payments.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('labor_payments.index')->with('error', $e->getMessage());
        }
    }
}
