<?php

namespace App\Http\Controllers;

use App\Models\employee_ledger_adjustment;
use App\Http\Controllers\Controller;
use App\Models\employee;
use App\Models\employee_ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeLedgerAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
        $type = $request->type ?? 'All';
        
        $adjustments = employee_ledger_adjustment::currentBranch()->whereBetween('date', [$start, $end]);
        if($type != "All")
        {
            $adjustments->where('type', $type);
          
        }
       
        $adjustments = $adjustments->get();
        
      
        $employees = employee::currentBranch()->get();

        return view('employees.adjustments.index', compact('adjustments', 'employees', 'start', 'end', 'type'));
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
            employee_ledger_adjustment::create(
                [
                    'employeeID' => $request->employeeID,
                    'userID' => auth()->user()->id,
                    'branchID' => auth()->user()->branchID,
                    'date' => $request->date,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                    'refID' => $ref
                ]
            );

            $employee = employee::find($request->employeeID);
            $user = auth()->user()->name;

            if($request->type == 'credit')
            {
                createEmployeeTransaction($request->employeeID, $request->date, $request->amount, 0, "Amount Adjusted: $request->notes", $ref);
            }
            else
            {
                createEmployeeTransaction($request->employeeID, $request->date, 0, $request->amount, "Amount Adjusted: $request->notes", $ref);
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
    public function show(employee_ledger_adjustment $employee_ledger_adjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(employee_ledger_adjustment $employee_ledger_adjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, employee_ledger_adjustment $employee_ledger_adjustment)
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
            employee_ledger_adjustment::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('employee_adjustments.index')->with('success', "Adjustment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('employee_adjustments.index')->with('error', $e->getMessage());
        }
    }
}
