<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\branches;
use App\Models\employee;
use App\Models\orderbooker_customers;
use App\Models\transactions;
use App\Models\User;
use Illuminate\Http\Request;

class EmpBalanceSheetReport extends Controller
{
    public function index()
    {
        $departments = employee::where('branchID', auth()->user()->branchID)->pluck('department')->toArray();
        $designations = employee::where('branchID', auth()->user()->branchID)->pluck('designation')->toArray();

        $departments = array_unique($departments);
        $designations = array_unique($designations);

        $departments = array_map(function ($department) {
            return ['id' => $department, 'name' => $department];
        }, $departments);

        $designations = array_map(function ($designation) {
            return ['id' => $designation, 'name' => $designation];
        }, $designations);
       
        return view('reports.EmpBalanceSheet.index', compact('departments', 'designations'));
    }

    public function data($filter, $value)    
    {
            $employees = employee::where('branchID', auth()->user()->branchID);
            if($filter == "Department")
            {
                $employees = $employees->where('department', $value);
            }
            if($filter == "Designation")
            {
                $employees = $employees->where('designation', $value);
            }

            $employees = $employees->get();

            foreach($employees as $employee)
            {
                $employee->balance = getEmployeeBalance($employee->id);
            }

            if($filter == "All")
            {
                $value = "All";
            }
           

        return view('reports.EmpBalanceSheet.details', compact('employees', 'filter', 'value'));
    }
}
