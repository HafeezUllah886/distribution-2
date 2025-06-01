<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalePaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $sale = sales::with('details', 'payments')->find($id);
        $amount = $sale->net;
        $paid = $sale->payments->sum('amount');
        $due = $amount - $paid;
        $currencies = currencymgmt::all();

        return view('sales.payments', compact('sale', 'due', 'currencies'));
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
            $sale = sales::find($request->salesID);
            $due = $sale->due();
            if($due < $request->amount)
            {
                throw new \Exception("Amount Exceeds Due");
            }
            sale_payments::create(
                [
                    'salesID'       => $sale->id,
                    'orderbookerID' => $sale->orderbookerID,
                    'branchID'      => auth()->user()->branchID,
                    'customerID'    => $sale->customerID,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'remarks'       => $request->remarks,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'notes'         => $request->notes,
                    'userID'        => auth()->id(),
                    'refID'         => $ref,
                ]
            );
            $user = auth()->user()->name;
            $customer = accounts::find($sale->customerID);
            $notes = "Payment of Inv No. $sale->id from $customer->title Method $request->method Notes : $request->notes";
            $notes1 = "Payment of Inv No. $sale->id submitted to $user Method $request->method Notes : $request->notes";
            createTransaction($sale->customerID, $request->date,0, $request->amount, $notes1, $ref);
            createUserTransaction(auth()->id(), $request->date,$request->amount, 0, $notes, $ref);
            createMethodTransaction(auth()->user()->id, $request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->remarks, $notes, $ref);

            if($request->method == 'Cash'){
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, $notes, $ref);
            }
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

    public function show($id)
    {
        $payment = sale_payments::find($id);

        return view('sales.receipt', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sale_payments $sale_payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, sale_payments $sale_payments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $ref)
    {
        try
        {
            DB::beginTransaction();
            sale_payments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('salePayment.index', $id)->with('success', "Sale Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('salePayment.index', $id)->with('error', $e->getMessage());
        }
    }
}
