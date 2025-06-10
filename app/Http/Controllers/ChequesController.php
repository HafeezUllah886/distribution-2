<?php

namespace App\Http\Controllers;

use App\Models\cheques;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChequesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cheques = cheques::where('userID', auth()->user()->id)->orderBy('cheque_date', 'asc')->get();
        return view('Finance.cheques.index', compact('cheques'));
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
                createTransaction($cheque->customerID, now(), $cheque->amount, 0, "Cheque Bounced Cheque No. $cheque->number, Bank: $cheque->bank, Clearing Date: $cheque->cheque_date", $ref);
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
