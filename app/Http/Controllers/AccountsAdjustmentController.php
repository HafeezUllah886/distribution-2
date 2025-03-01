<?php

namespace App\Http\Controllers;

use App\Models\accountsAdjustment;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accountsAdjustments = accountsAdjustment::currentBranch()->get();
        $accounts = accounts::currentBranch()->get();
        return view('Finance.accounts_adjustments.index', compact('accountsAdjustments', 'accounts'));
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
            accountsAdjustment::create(
                [
                    'accountID' => $request->accountID,
                    'userID' => auth()->user()->id,
                    'branchID' => auth()->user()->branchID,
                    'date' => $request->date,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                    'refID' => $ref
                ]
            );

            $account = accounts::find($request->accountID);
            $user = auth()->user()->name;

            if($request->type == 'credit')
            {
                createTransaction($request->accountID, $request->date, $request->amount, 0, "Amount Adjusted", $ref);
            }
            else
            {
                createTransaction($request->accountID, $request->date, 0, $request->amount, "Amount Adjusted", $ref);
            }

           DB::commit();
            return back()->with('success', "Adjustment Created");
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
    public function show(accountsAdjustment $accountsAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(accountsAdjustment $accountsAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, accountsAdjustment $accountsAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(accountsAdjustment $accountsAdjustment)
    {
        //
    }


    public function delete($ref)
    {
        try
        {
            DB::beginTransaction();
            accountsAdjustment::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('accounts_adjustments.index')->with('success', "Adjustment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('accounts_adjustments.index')->with('error', $e->getMessage());
        }
    }
}
