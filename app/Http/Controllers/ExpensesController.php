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
        foreach($currencies as $currency)
        {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        $categories = expense_categories::currentBranch()->get();
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
                    'cheque_date' => $request->cheque_date,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );

            $notes = "Expense - Method ".$request->method." Notes : ".$request->notes;
            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);
           
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
    public function show($id)
    {
        $expense = expenses::find($id);
        $currencies = currencymgmt::all();

        if($expense->method == "Cash")
        {
          
            foreach($currencies as $currency)
            {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $expense->refID)->first();

                $currency->qty = $currenyTransaction->db ?? 0;
            }

        }

        return view('Finance.expense.receipt', compact('expense', 'currencies'));
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
            cheques::where('refID', $ref)->delete();
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
