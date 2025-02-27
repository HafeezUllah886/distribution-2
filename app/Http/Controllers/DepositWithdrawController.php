<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\deposit_withdraw;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositWithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trans = deposit_withdraw::currentBranch()->orderBy('id', 'desc')->get();
        $accounts = accounts::business()->currentBranch()->get();
        $currencies = currencymgmt::all();

        return view('Finance.deposit_withdraw.index', compact('trans', 'accounts', 'currencies'));
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
        try
        { 
            /* $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]); */
            DB::beginTransaction();
            $ref = getRef();
            deposit_withdraw::create(
                [
                    'accountID' => $request->accountID,
                    'userID' => auth()->user()->id,
                    'branchID' => auth()->user()->branchID,
                    'date' => $request->date,
                    'type' => $request->type,
                    'amount' => $request->total,
                    'notes' => $request->notes,
                    'refID' => $ref
                ]
            );

            $account = accounts::find($request->accountID);
            $user = auth()->user()->name;

            if($request->type == 'Deposit')
            {
                createTransaction($request->accountID, $request->date, $request->total, 0, "Deposit by $user: ".$request->notes, $ref);

                createUserTransaction(auth()->id(), $request->date, 0, $request->total, "Bank Deposit to $account->title : ".$request->notes, $ref);
                createCurrencyTransaction(auth()->id(), $request->currencyID, $request->currency, 'db', $request->date, "Bank Deposit to $account->title : ".$request->notes, $ref);
            }
            else
            {
                createTransaction($request->accountID, $request->date, 0, $request->total, "Withdraw by $user: ".$request->notes, $ref);
                createUserTransaction(auth()->id(), $request->date, $request->total, 0, "Withdraw from $account->title : ".$request->notes, $ref);
                createCurrencyTransaction(auth()->id(), $request->currencyID, $request->currency, 'cr', $request->date, "Withdraw from $account->title : ".$request->notes, $ref);
            }
            if($request->has('file'))
            {
                createAttachment($request->file('file'), $ref);
            }

           DB::commit();
            return back()->with('success', "Transaction Created");
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
    public function show(deposit_withdraw $deposit_withdraw)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(deposit_withdraw $deposit_withdraw)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, deposit_withdraw $deposit_withdraw)
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
            deposit_withdraw::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            DB::commit();
            deleteAttachment($ref);
            session()->forget('confirmed_password');
            return redirect()->route('deposit_withdraw.index')->with('success', "Transaction Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('deposit_withdraw.index')->with('error', $e->getMessage());
        }
    }
}
