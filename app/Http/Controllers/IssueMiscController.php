<?php

namespace App\Http\Controllers;

use App\Models\issue_misc;
use App\Http\Controllers\Controller;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\employee;
use App\Models\employee_ledger;
use App\Models\employees_payment_cats;
use App\Models\method_transactions;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssueMiscController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->from ?? date('Y-m-d');
        $end = $request->to ?? date('Y-m-d');
        $desig = $request->designation ?? "All";
        $dept = $request->department ?? "All";   
        $misc = issue_misc::currentBranch()->whereBetween('date', [$start, $end]);
        if($desig != "All"){
            $misc = $misc->whereHas('employee', function ($query) use ($desig) {
                $query->where('designation', $desig);
            });
        }
        if($dept != "All"){
            $misc = $misc->whereHas('employee', function ($query) use ($dept) {
                $query->where('department', $dept);
            });
        }
        $miscs = $misc->get();
        $employees = employee::currentBranch()->get();
        $categories = employees_payment_cats::all();

        $designations = employee::currentBranch()->get()->unique('designation')->pluck('designation')->toArray();
        $departments = employee::currentBranch()->get()->unique('department')->pluck('department')->toArray();
        return view('employees.issue_misc.index', compact('miscs', 'employees', 'start', 'end', 'desig', 'dept', 'designations', 'departments', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = $request->employee;
       
        $employee = employee::find($employee);
        $category = employees_payment_cats::find($request->category);
        $balance = getEmployeeBalance($employee->id);

        $currencies = currencymgmt::all();
        foreach($currencies as $currency)
        {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        return view('employees.issue_misc.create', compact('employee', 'currencies', 'balance', 'category'));
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
          $category = employees_payment_cats::find($request->categoryID);

            $ref = getRef();
            $misc = issue_misc::create([
                'employeeID' => $request->employeeID,
                'branchID' => auth()->user()->branchID,
                'catID' => $request->categoryID,
                'amount' => $request->amount,
                'date' => $request->date,
                'method' => $request->method,
                'number' => $request->number,
                'bank' => $request->bank,
                'cheque_date' => $request->cheque_date,
                'notes' => $request->notes,
                'refID' =>  $ref,
            ]);

            createEmployeeTransaction($request->employeeID, $request->date, $request->amount, $request->amount, 'Misc Issued - Category : ' . $category->name . ' notes : ' . $request->notes, $ref);
            createUserTransaction(auth()->user()->id, $request->date, 0, $request->amount, 'Misc Issued to ' . $employee->name . ' - Category : ' . $category->name . ' notes : ' . $request->notes, $ref);
            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, 'Misc Issued to ' . $employee->name . ' - Category : ' . $category->name . ' notes : ' . $request->notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, 'Misc Issued to ' . $employee->name . ' - Category : ' . $category->name . ' notes : ' . $request->notes, $ref);
            }
            
            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }
            DB::commit();
            return to_route('issue_misc.index')->with('success', 'Misc Issued Successfully');
            
        } catch (\Exception $th) {
            DB::rollBack();
            return to_route('issue_misc.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $misc = issue_misc::find($id);
        $currencies = currencymgmt::all();
        if($misc->method == "Cash")
        {
          
            foreach($currencies as $currency)
            {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $misc->refID)->first();

                $currency->qty = $currenyTransaction->db ?? 0;
            }

        }
        return view('employees.issue_misc.receipt', compact('misc', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(issue_misc $issue_misc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, issue_misc $issue_misc)
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
            issue_misc::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('issue_misc.index')->with('success', "Misc Deleted");
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('issue_misc.index')->with('error', $e->getMessage());
        }
    }
}
