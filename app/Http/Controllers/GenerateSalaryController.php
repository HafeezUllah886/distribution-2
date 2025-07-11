<?php

namespace App\Http\Controllers;

use App\Models\generate_salary;
use App\Http\Controllers\Controller;
use App\Models\employee;
use App\Models\employee_ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenerateSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->from ?? firstDayOfMonth();
        $endDate = $request->to ?? lastDayOfMonth();
        $salaries = generate_salary::whereBetween('date', [$startDate, $endDate])->currentBranch()->get();
        return view('employees.generate_salary.index', compact('salaries', 'startDate', 'endDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = employee::currentBranch()->active()->get();
        return view('employees.generate_salary.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            DB::beginTransaction();
        $generated = 0;
        $skipped = 0;

        $month = $request->month . '-' . 1;

        $month_name = date('M Y', strtotime($month));

        foreach ($request->employees as $index => $employeeId) {

            $check = generate_salary::where('employeeID', $employeeId)->where('month', $month)->count();
            if ($check > 0) {
                $skipped++;
               continue;
            }
            $ref = getRef();
            generate_salary::create([
                'branchID' => auth()->user()->branchID,
                'employeeID' => $employeeId,
                'salary' => $request->salary[$index],
                'month' => $month,
                'date' => $request->date,
                'refID' => $ref,
            ]);
            createEmployeeTransaction($employeeId, $request->date, 0, $request->salary[$index], 'Salary generated for the month of ' . $month_name, $ref);
            $generated++;
        }
        DB::commit();

        return redirect()->route('generate_salary.index')->with('success', "$generated salaries generated successfully. $skipped salaries skipped (already generated).");
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', $e->getMessage());
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(generate_salary $generate_salary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(generate_salary $generate_salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, generate_salary $generate_salary)
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
            $salary = generate_salary::where('refID', $ref)->first();
            if (!$salary) {
                return redirect()->back()->with('error', 'Salary not found');
            }
            employee_ledger::where('refID', $ref)->delete();
            $salary->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('generate_salary.index')->with('success', 'Salary deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
