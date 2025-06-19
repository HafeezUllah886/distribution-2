<?php

namespace App\Http\Controllers;

use App\Models\cheques;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\User;
use Illuminate\Http\Request;

class ChequesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? lastDayOfMonth();
        $orderbooker = $request->orderbookerID ?? 'All';
        $status = $request->status ?? "All";

        $cheques = cheques::where('userID', auth()->user()->id)->orderBy('cheque_date', 'asc')->whereBetween('cheque_date', [$start, $end]);
        if($orderbooker != 'All')
        {
            $cheques->where('orderbookerID', $orderbooker);
        }
        if($status != 'All')
        {
            $cheques->where('status', $status);
        }

        $cheques = $cheques->get();
        $orderbookers = User::orderbookers()->currentBranch()->get();

        return view('Finance.cheques.index', compact('cheques', 'start', 'end', 'orderbooker', 'status', 'orderbookers'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $status)
    {
        $cheque = cheques::findOrFail($id);
        if($cheque->userID == auth()->user()->id)
        {
            $cheque->update([
                'status' => $status,
            ]);
            if($status == 'bounced')
            {
                $ref = getRef();
                $customer = accounts::find($cheque->customerID);
                createTransaction($cheque->customerID, now(), $cheque->amount, 0, "Cheque Bounced Cheque No. $cheque->number, Bank: $cheque->bank, Clearing Date: $cheque->cheque_date", $ref);
                createUserTransaction(Auth()->id(), now(), 0, $cheque->amount, "Cheque Bounced of $customer->title Cheque No. $cheque->number, Bank: $cheque->bank, Clearing Date: $cheque->cheque_date", $ref);
                createMethodTransaction(Auth()->id(), "Cheque", 0, $cheque->amount, now(), $cheque->number, $cheque->bank, $cheque->cheque_date, "Cheque Bounced of $customer->title", $ref);
            }
            return redirect()->back()->with('success', 'Cheque status updated successfully');
        }
        return redirect()->back()->with('error', 'You are not authorized to update this cheque');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(cheques $cheques)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, cheques $cheques)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(cheques $cheques)
    {
        //
    }
}
