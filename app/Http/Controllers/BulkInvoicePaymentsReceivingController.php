<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\bulk_payments;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\transactions;
use App\Models\transactions_que;
use App\Models\User;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkInvoicePaymentsReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? now()->toDateString();
        $customerID = $request->customerID;
        $method = $request->method;

        $customers = accounts::customer()->currentBranch()->get();
        $orderBookers = User::orderbookers()->currentBranch()->get();

        $areas = area::currentBranch()->get();

        $payments = bulk_payments::currentBranch()->orderBy('id', 'desc')->whereBetween('date', [$start, $end]);
        if($customerID)
        {
            $payments = $payments->where('customerID', $customerID);
        }
        if($method)
        {
            $payments = $payments->where('method', $method);
        }
        $payments = $payments->get();

        return view('Finance.bulk_payment.index', compact('customers', 'orderBookers', 'areas', 'payments', 'start', 'end', 'customerID', 'method'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $invoices = sales::where('customerID', $request->customerID)->where('orderbookerID', $request->orderbookerID)->unpaidOrPartiallyPaid()->get();
        $currencies = currencymgmt::all();
        $orderbookerID = $request->orderbookerID;

        return view('Finance.bulk_payment.create', compact('invoices', 'currencies', 'orderbookerID'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{ 
            DB::beginTransaction();
            $ref = getRef();
            if($request->amount != $request->netAmount)
            {
                throw new \Exception("Currencies Total does not match Invoices Net Amount. Please check your values.");
            }
           
            foreach($request->invoiceID as $key => $invoiceID)
            {
                $sale = sales::find($invoiceID);
                if($request->invamount[$key] > 0)
                {
                sale_payments::create([
                    'salesID' => $invoiceID,
                    'customerID' => $sale->customerID,
                    'orderbookerID' => $sale->orderbookerID,
                    'date' => $request->date,
                    'amount' => $request->invamount[$key],
                    'notes' => $request->notes,
                    'branchID' => auth()->user()->branchID,
                    'method' => $request->method,
                    'bank' => $request->bank,
                    'number' => $request->number,
                    'cheque_date' => $request->cheque_date,
                    'userID' => auth()->id(),
                    'refID' => $ref
                ]);
                $saleIDs[] = $invoiceID;
                }
            }
            $net = $request->netAmount;
            $saleIDs = implode(',', $saleIDs);
            bulk_payments::create([
                'customerID' => $sale->customerID,
                'orderbookerID' => $sale->orderbookerID,
                'date' => $request->date,
                'amount' => $request->amount,
                'notes' => $request->notes,
                'branchID'      => auth()->user()->branchID,
                'method' => $request->method,
                'bank' => $request->bank,
                'number' => $request->number,
                'cheque_date' => $request->cheque_date,
                'userID' => auth()->id(),
                'refID' => $ref,
                'invoiceIDs' => $saleIDs
            ]);
            $notes = "Bulk Payment of Inv No. $saleIDs from " . $sale->customer->title . " method " . $request->method . " notes : " . $request->notes;
            $notes1 = "Bulk Payment of Inv No. $saleIDs to " . auth()->user()->name . " method " . $request->method . " notes : " . $request->notes;
            createTransaction($request->customerID, $request->date,0, $net, $notes1, $ref, $sale->orderbookerID);
            createUserTransaction(auth()->id(), $request->date,$net, 0, $notes, $ref);
           createMethodTransaction(auth()->user()->id, $request->method, $net,0, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);
           if($request->method == "Cash")
           {
            createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, $notes, $ref);
           }
           if($request->method == 'Cheque'){
            saveCheque($sale->customerID, auth()->id(), $request->orderbookerID, $request->cheque_date, $request->amount,$request->number,$request->bank,$request->notes,$ref);
        }

           if($request->has('file')){
            createAttachment($request->file('file'), $ref);
        }

            DB::commit();
                return to_route('bulk_payment.index')->with('success', "Bulk Payment Saved");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = bulk_payments::find($id);
        $currencies = currencymgmt::all();
        if($payment->method == "Cash")
        {
            foreach($currencies as $currency)
            {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $payment->refID)->first();

                $currency->qty = $currenyTransaction->cr ?? 0;
            }

        }
        return view('Finance.bulk_payment.receipt', compact('payment', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        try{
            DB::beginTransaction();
            bulk_payments::where('refID', $ref)->delete();
            sale_payments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('bulk_payment.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('bulk_payment.index')->with('error', $e->getMessage());
        }

    }

    public function getCustomersByArea($area)
    {
        if($area == 'All')
        {
            $customers = accounts::customer()->currentBranch()->select('id as value', 'title as text')->get();
        }
        else
        {
            $customers = accounts::customer()->where('areaID', $area)->select('id as value', 'title as text')->get();
        }
        
        return response()->json($customers);

    }
}
