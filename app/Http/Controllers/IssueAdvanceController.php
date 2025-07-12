<?php

namespace App\Http\Controllers;

use App\Models\issue_advance;
use App\Http\Controllers\Controller;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\employee;
use App\Models\employee_ledger;
use App\Models\method_transactions;
use App\Models\transactions;
use App\Models\users_transactions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssueAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->from ?? firstDayOfMonth();
        $end = $request->to ?? lastDayOfMonth();
        $desig = $request->designation ?? "All";
        $dept = $request->department ?? "All";   
        $advance = issue_advance::currentBranch()->whereBetween('date', [$start, $end]);
        if($desig != "All"){
            $advance = $advance->whereHas('employee', function ($query) use ($desig) {
                $query->where('designation', $desig);
            });
        }
        if($dept != "All"){
            $advance = $advance->whereHas('employee', function ($query) use ($dept) {
                $query->where('department', $dept);
            });
        }
        $advances = $advance->get();
        $employees = employee::currentBranch()->get();

        $designations = employee::currentBranch()->get()->unique('designation')->pluck('designation')->toArray();
        $departments = employee::currentBranch()->get()->unique('department')->pluck('department')->toArray();
        return view('employees.issue_advance.index', compact('advances', 'employees', 'start', 'end', 'desig', 'dept', 'designations', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = $request->employee;
       
        $employee = employee::find($employee);
        $balance = getEmployeeBalance($employee->id);

        $currencies = currencymgmt::all();
        foreach($currencies as $currency)
        {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        return view('employees.issue_advance.create', compact('employee', 'currencies', 'balance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        try {

            DB::beginTransaction();

            if(!checkMethodExceed($request->method, auth()->user()->id, $request->amount))
           {
            throw new \Exception("Method Amount Exceed");
           }
           if(!checkUserAccountExceed(auth()->user()->id, $request->amount))
           {
            throw new \Exception("User Account Amount Exceed");
           }
          if($request->method == 'Cash')
          {
            if(!checkCurrencyExceed(auth()->user()->id, $request->currencyID, $request->qty))
            {
                throw new \Exception("Currency Qty Exceed");
            }
          }

          $employee = employee::find($request->employeeID);

          $balance = getEmployeeBalance($request->employeeID) + $employee->limit;
          if($request->amount > $balance)
          {
            throw new \Exception("limit Exceed");
          }
            $ref = getRef();
            $advance = issue_advance::create([
                'employeeID' => $request->employeeID,
                'branchID' => auth()->user()->branchID,
                'advance' => $request->amount,
                'date' => $request->date,
                'method' => $request->method,
                'number' => $request->number,
                'bank' => $request->bank,
                'cheque_date' => $request->cheque_date,
                'notes' => $request->notes,
                'refID' =>  $ref,
            ]);

            createEmployeeTransaction($request->employeeID, $request->date, $request->amount, 0, 'Advance Issued- notes : ' . $request->notes, $ref);
            createUserTransaction(auth()->user()->id, $request->date, 0, $request->amount, 'Advance Issued to ' . $employee->name . '- notes : ' . $request->notes, $ref);
            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, 'Advance Issued to ' . $employee->name . '- notes : ' . $request->notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, 'Advance Issued to ' . $employee->name . '- notes : ' . $request->notes, $ref);
            }
            
            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }
            DB::commit();
            return to_route('issue_advance.index')->with('success', 'Advance Issued Successfully');
            
        } catch (\Exception $th) {
            DB::rollBack();
            return to_route('issue_advance.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $advance = issue_advance::find($id);
        $currencies = currencymgmt::all();
        if($advance->method == "Cash")
        {
          
            foreach($currencies as $currency)
            {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $advance->refID)->first();

                $currency->qty = $currenyTransaction->db ?? 0;
            }

        }
        return view('employees.issue_advance.receipt', compact('advance', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(issue_advance $issue_advance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, issue_advance $issue_advance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($ref)
    {
        try {
            DB::beginTransaction();
            issue_advance::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('issue_advance.index')->with('success', "Advance Deleted");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('issue_advance.index')->with('error', $e->getMessage());
        }
    }
}
