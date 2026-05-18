<?php

namespace App\Http\Controllers;

use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\employee;
use App\Models\issue_salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IssueSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        $desig = $request->designation ?? 'All';
        $dept = $request->department ?? 'All';
        $salaries = issue_salary::currentBranch();
        if ($month != 'All') {
            $salaries = $salaries->where('month', $month.'-01');
        }
        if ($desig != 'All') {
            $salaries = $salaries->whereHas('employee', function ($query) use ($desig) {
                $query->where('designation', $desig);
            });
        }
        if ($dept != 'All') {
            $salaries = $salaries->whereHas('employee', function ($query) use ($dept) {
                $query->where('department', $dept);
            });
        }
        $salaries = $salaries->get();
        $employees = employee::currentBranch()->get();

        $designations = employee::currentBranch()->get()->unique('designation')->pluck('designation')->toArray();
        $departments = employee::currentBranch()->get()->unique('department')->pluck('department')->toArray();

        return view('employees.issue_salary.index', compact('salaries', 'employees', 'month', 'desig', 'dept', 'designations', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $employee = $request->employee;
        $month = $request->month.'-01';
        $check = issue_salary::where('employeeID', $employee)->where('month', $month)->first();
        if ($check) {
            return redirect()->back()->with('error', 'Salary already issued to this employee for this month');
        }
        $employee = employee::find($employee);
        $balance = getEmployeeBalance($employee->id);

        $currencies = currencymgmt::all();
        foreach ($currencies as $currency) {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        $month = $request->month;

        return view('employees.issue_salary.create', compact('employee', 'month', 'currencies', 'balance', 'month'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            DB::beginTransaction();

            if (! checkMethodExceed($request->method, auth()->user()->id, $request->amount)) {
                throw new \Exception('Method Amount Exceed');
            }
            if (! checkUserAccountExceed(auth()->user()->id, $request->amount)) {
                throw new \Exception('User Account Amount Exceed');
            }
            if ($request->method == 'Cash') {
                if (! checkCurrencyExceed(auth()->user()->id, $request->currencyID, $request->qty)) {
                    throw new \Exception('Currency Qty Exceed');
                }
            }
            $ref = getRef();
            $month = $request->month.'-01';
            $salary = issue_salary::create([
                'employeeID' => $request->employeeID,
                'branchID' => auth()->user()->branchID,
                'month' => $month,
                'salary' => $request->amount,
                'date' => $request->date,
                'method' => $request->method,
                'number' => $request->number,
                'bank' => $request->bank,
                'cheque_date' => $request->cheque_date,
                'notes' => $request->notes,
                'refID' => $ref,
            ]);

            $month = date('M Y', strtotime($month));
            $employee = employee::find($request->employeeID);

            createEmployeeTransaction($request->employeeID, $request->date, $request->amount, 0, 'Salary Issued for '.$month.'- notes : '.$request->notes, $ref);
            createUserTransaction(auth()->user()->id, $request->date, 0, $request->amount, 'Salary Issued for '.$month.' to '.$employee->name.'- notes : '.$request->notes, $ref);
            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, 'Salary Issued for '.$month.' to '.$employee->name.'- notes : '.$request->notes, $ref);

            if ($request->method == 'Cash') {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, 'Salary Issued for '.$month.' to '.$employee->name.'- notes : '.$request->notes, $ref);
            }

            if ($request->has('file')) {
                createAttachment($request->file('file'), $ref);
            }
            DB::commit();

            return to_route('issue_salary.index')->with('success', 'Salary Issued Successfully');

        } catch (\Exception $th) {
            DB::rollBack();

            return to_route('issue_salary.index')->with('error', $th->getMessage());
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $salary = issue_salary::find($id);
        $currencies = currencymgmt::all();
        if ($salary->method == 'Cash') {

            foreach ($currencies as $currency) {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $salary->refID)->first();

                $currency->qty = $currenyTransaction->db ?? 0;
            }

        }

        return view('employees.issue_salary.receipt', compact('salary', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(issue_salary $issue_salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, issue_salary $issue_salary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($ref)
    {
        $salary = issue_salary::where('refID', $ref)->first();
        $employee = employee::find($salary->employeeID);
        $designation = $employee->designation;
        $department = $employee->department;
        $address = $employee->address;
        $salaryMonth = date('F Y', strtotime($salary->month));
        $notes = "Issue Salary Date: $salary->date | Employee: $employee->name  | Designation :  $designation | Department : $department | Address :  $address | Salary Month : $salaryMonth | Amount: $salary->salary | Notes: $salary->notes";
        $delete = storeDeleteRequest(auth()->user()->id, $salary->branchID, $salary->refID, 'issue_salary', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('issue_salary.index')->with('success', 'Issue Salary Delete Request Sent to Branch Admin');
    }
}
