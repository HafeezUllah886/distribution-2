<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\employee;
use App\Models\employees_payment_cats;
use App\Models\expense_categories;
use App\Models\expenses;
use App\Models\issue_advance;
use App\Models\issue_misc;
use App\Models\issue_salary;
use App\Models\paymentReceiving;
use App\Models\payments;
use App\Models\paymentsReceiving;
use App\Models\sale_payments;
use App\Models\staffPayments;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityReportController extends Controller
{
    public function index()
    {
        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }
        return view('reports.activity_report.index', compact('branches'));
    }

    public function data(Request $request)
    {
       $from = $request->from;
       $to = $request->to;
       $branch = $request->branch;

       $payment_accounts = accounts::orderBy('type', 'asc')->where('branchID', $branch)->get();

        foreach($payment_accounts as $payment_account)
        {
            $payment_account->payments = payments::where('receiverID', $payment_account->id)->whereBetween('date', [$from, $to])->get();

            $payment_account->receivings = paymentsReceiving::where('depositerID', $payment_account->id)->whereBetween('date', [$from, $to])->get();
        }

        $staffs = User::where("branchID", $branch)->orderBy('role', 'asc')->get();

        foreach($staffs as $staff)
        {
            $staff->payments = staffPayments::where('fromID', $staff->id)->whereBetween('date', [$from, $to])->get();
        }

        $customers = accounts::customer()->where('branchID', $branch)->orderBy('id', 'asc')->get();

        foreach($customers as $customer)
        {
            $customer->salePayments = sale_payments::where('customerID', $customer->id)->whereBetween('date', [$from, $to])->get();
        }

        $expense_categories = expense_categories::all();

        foreach($expense_categories as $expense_category)
        {
            $expense_category->trans = expenses::where('categoryID', $expense_category->id)->where('branchID', $branch)->whereBetween('date', [$from, $to])->get();
        }

        $salaries = issue_salary::where('branchID', $branch)->whereBetween('date', [$from, $to])->orderBy('employeeID', 'asc')->get();

        $advances = issue_advance::where('branchID', $branch)->whereBetween('date', [$from, $to])->orderBy('employeeID', 'asc')->get();

        $emp_payment_cats = employees_payment_cats::all();

        foreach($emp_payment_cats as $emp_payment_cat)
        {
            $emp_payment_cat->trans = issue_misc::where('catID', $emp_payment_cat->id)->where('branchID', $branch)->whereBetween('date', [$from, $to])->get();
        }

        $branch = branches::find($branch);
        $branch = $branch->name;

        return view('reports.activity_report.details', compact('from', 'to', 'branch', 'payment_accounts', 'staffs', 'customers', 'expense_categories', 'salaries', 'advances', 'emp_payment_cats'));
    }
}
