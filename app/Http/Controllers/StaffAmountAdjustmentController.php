<?php

namespace App\Http\Controllers;

use App\Models\staffAmountAdjustment;
use App\Http\Controllers\Controller;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\User;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAmountAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staffAdjustments = staffAmountAdjustment::currentBranch()->get();
        $staffs = User::currentBranch()->get();
        $currencies = currencymgmt::all();
        return view('finance.staff_amount_adjustments.index', compact('staffAdjustments', 'staffs', 'currencies'));
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
            DB::beginTransaction();
            $ref = getRef();
            staffAmountAdjustment::create(
            [
                'staffID' => $request->staffID,
                'userID' => auth()->user()->id,
                'branchID' => auth()->user()->branchID,
                'date' => $request->date,
                'type' => $request->type,
                'amount' => $request->total,
                'notes' => $request->notes,
                'refID' => $ref
            ]
        );

        $staff = User::find($request->staffID);
        $user = auth()->user()->name;

        if($request->type == 'credit')
        {
            createUserTransaction($request->staffID, $request->date, $request->total, 0, "Amount Adjusted", $ref);
            createCurrencyTransaction($request->staffID, $request->currencyID, $request->qty, 'cr', $request->date, "Amount Adjusted", $ref);
        }
        else
        {
            createUserTransaction($request->staffID, $request->date, 0, $request->total, "Amount Adjusted", $ref);
            createCurrencyTransaction($request->staffID, $request->currencyID, $request->qty, 'db', $request->date, "Amount Adjusted", $ref);
        }
        if($request->has('file'))
        {
            createAttachment($request->file('file'), $ref);
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
    public function show(staffAmountAdjustment $staffAmountAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(staffAmountAdjustment $staffAmountAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, staffAmountAdjustment $staffAmountAdjustment)
    {
        //
    }
    
    public function delete($ref)
    {
        try
        {
            DB::beginTransaction();
            staffAmountAdjustment::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('staff_amounts_adjustments.index')->with('success', "Adjustment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('staff_amounts_adjustments.index')->with('error', $e->getMessage());
        }
    }
}

