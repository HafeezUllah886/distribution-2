<?php

namespace App\Http\Controllers;

use App\Models\staffPayments;
use App\Http\Controllers\Controller;
use App\Models\currencymgmt;
use App\Models\User;
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
        $users = User::where('branchID', auth()->user()->branchID)->whereNotIn('role', ['Admin', 'Order Booker'])->where('id', '!=', auth()->user()->id)->get();
        $currencies = currencymgmt::all();
        return view('Finance.staff_payments.index', compact('receivings', 'users', 'currencies'));
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
            staffPayments::create(
                [
                    'fromID'        => $request->fromID,
                    'date'          => $request->date,
                    'amount'        => $request->total,
                    'notes'         => $request->notes,
                    'receivedBy'    => auth()->id(),
                    'refID'         => $ref,
                ]
            );
            $staff = User::find($request->fromID);
            $user_name = auth()->user()->name;
            createUserTransaction(auth()->id(), $request->date,$request->total, 0, "Payment received from staff: $staff->name", $ref);
            createUserTransaction($request->fromID, $request->date,0, $request->total, "Payment submitted to $user_name", $ref);
            createCurrencyTransaction(auth()->id(), $request->currencyID, $request->qty, 'cr', $request->date, "Payment received from staff: $staff->name", $ref);
            createCurrencyTransaction($request->fromID, $request->currencyID, $request->qty, 'db', $request->date, "Payment submitted to $user_name", $ref);
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
    public function destroy(staffPayments $staffPayments)
    {
        //
    }
}
