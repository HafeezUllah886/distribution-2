<?php

namespace App\Http\Controllers;

use App\Models\customerPayments;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\transactions;
use Database\Seeders\currencies_seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receivings = customerPayments::where('branchID', auth()->user()->branchID)->orderBy('id', 'desc')->get();
        $customers = accounts::customer()->currentBranch()->get();
        $currencies = currencymgmt::all();
        return view('Finance.customer_payments.index', compact('receivings', 'customers', 'currencies'));
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
        
        try{ 
            DB::beginTransaction();
            $ref = getRef();
            customerPayments::create(
                [
                    'customerID'    => $request->customerID,
                    'date'          => $request->date,
                    'amount'        => $request->total,
                    'branchID'      => auth()->user()->branchID,
                    'notes'         => $request->notes,
                    'receivedBy'    => auth()->id(),
                    'refID'         => $ref,
                ]
            );
            $customer = accounts::find($request->customerID);
            $user_name = auth()->user()->name;
            createTransaction($request->customerID, $request->date,0, $request->total, "Payment submitted to $user_name", $ref);
            createUserTransaction(auth()->id(), $request->date,$request->total, 0, "Payment received from customer: $customer->title", $ref);
            createCurrencyTransaction(auth()->id(), $request->currencyID, $request->qty, 'cr', $request->date, "Payment received from customer: $customer->title", $ref);
            if($request->has('file')){
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();
            return back()->with('success', "Payment Saved");
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
        $receiving = customerPayments::find($id);
        return view('Finance.customer_payments.receipt', compact('receiving'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(customerPayments $customerPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, customerPayments $customerPayments)
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
            customerPayments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('customer_payments.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('customer_payments.index')->with('error', $e->getMessage());
        }
    }
}
