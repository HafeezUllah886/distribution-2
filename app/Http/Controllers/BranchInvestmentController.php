<?php

namespace App\Http\Controllers;

use App\Models\BranchInvestment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchInvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfCurrentYear();
        $to = $request->to ?? lastDayOfCurrentYear();
        $investments = BranchInvestment::currentBranch()->whereBetween('date', [$from, $to])->get();

        return view('Finance.branch_investment.index', compact('investments', 'from', 'to'));
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
        $request->validate([
            'investerName' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        $ref = getRef();

        BranchInvestment::create([
            'investerName' => $request->investerName,
            'amount' => $request->amount,
            'date' => $request->date,
            'notes' => $request->notes,
            'branchID' => auth()->user()->branchID,
            'refID' => $ref,
        ]);

        return redirect()->route('branch_investment.index')->with('success', 'Branch Investment created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(BranchInvestment $branchInvestment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BranchInvestment $branchInvestment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BranchInvestment $branchInvestment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($refID)
    {
        try
        {
            DB::beginTransaction();
            BranchInvestment::where('refID', $refID)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('branch_investment.index')->with('success', "Branch Investment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('branch_investment.index')->with('error', $e->getMessage());
        }
    }
}
