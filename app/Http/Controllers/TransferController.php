<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\transactions;
use App\Models\transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
        $transfers = transfer::orderby('id', 'desc')->currentBranch()->whereBetween('date', [$start, $end])->get();
        $accounts = accounts::business()->currentBranch()->get();
        return view('Finance.transfer.index', compact('transfers', 'accounts', 'start', 'end'));
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
        $request->validate(
            [
                'to' => 'different:from'
            ],
            [
                'to.different' => "From and To Accounts Must be different"
            ]
        );

        try
        {
            DB::beginTransaction();
            $ref = getRef();
            $transfer = transfer::create(
                [
                    'from' => $request->from,
                    'to' => $request->to,
                    'userID' => auth()->id(),
                    'branchID' => auth()->user()->branchID,
                    'date' => $request->date,
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );
            $fromAccount = $transfer->fromAccount->title;
            $toAccount = $transfer->toAccount->title;

            createTransaction($request->from,$request->date, 0, $request->amount, "Transfered to $toAccount :" .$request->notes, $ref, 0);
            createTransaction($request->to, $request->date, $request->amount, 0, "Transfered from $fromAccount :" .$request->notes, $ref, 0);

            DB::commit();
            return back()->with('success', "Transfered Successfully");
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
    public function show(transfer $transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, transfer $transfer)
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
            transfer::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();

            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('transfers.index')->with('success', "Transfer Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('transfers.index')->with('error', $e->getMessage());
        }
    }
}
