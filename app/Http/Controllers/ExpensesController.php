<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\expense_categories;
use App\Models\expenses;
use App\Models\method_transactions;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->start ?? date('Y-m-d');
        $to = $request->end ?? date('Y-m-d');
        $categoryID = $request->category ?? "All";
        if($categoryID == "All")
        {
            $expenses = expenses::currentBranch()->whereBetween('date', [$from, $to])->orderby('id', 'desc')->get();
        }
        else
        {
            $expenses = expenses::currentBranch()->whereBetween('date', [$from, $to])->where('categoryID', $categoryID)->orderby('id', 'desc')->get();
        }
        $currencies = currencymgmt::all();
        $categories = expense_categories::all();
        return view('Finance.expense.index', compact('expenses', 'currencies', 'categories', 'from', 'to', 'categoryID'));
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
            expenses::create(
                [
                    'userID' => auth()->user()->id,
                    'amount' => $request->amount,
                    'branchID' => auth()->user()->branchID,
                    'categoryID' => $request->category,
                    'date' => $request->date,
                    'method' => $request->method,
                    'number' => $request->number,
                    'bank' => $request->bank,
                    'remarks' => $request->remarks,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );

            $notes = "Expense - Method ".$request->method." Notes : ".$request->notes;
            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->remarks, $notes, $ref);
           
            createUserTransaction(auth()->user()->id, $request->date,0, $request->amount, $notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, $notes, $ref);
            }
            
            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();
            return back()->with('success', 'Expense Saved');
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
    public function show(expenses $expenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(expenses $expenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, expenses $expenses)
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
            expenses::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('expenses.index')->with('success', "Expense Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('expenses.index')->with('error', $e->getMessage());
        }
    }
}
