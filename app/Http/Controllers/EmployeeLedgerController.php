<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\employee;
use App\Models\employee_ledger;
use Illuminate\Http\Request;

class EmployeeLedgerController extends Controller
{
    public function statement($id, $from, $to)
    {
        $employee = employee::find($id);

        $transactions = employee_ledger::where('employeeID', $id)->whereBetween('date', [$from, $to])->orderBy('date', 'asc')->orderBy('refID', 'asc')->get();

        $pre_cr = employee_ledger::where('employeeID', $id)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = employee_ledger::where('employeeID', $id)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = employee_ledger::where('employeeID', $id)->sum('cr');
        $cur_db = employee_ledger::where('employeeID', $id)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('employees.statment', compact('employee', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to'));
    }
}
