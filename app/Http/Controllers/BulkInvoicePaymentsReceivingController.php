<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\bulk_payments;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\transactions;
use App\Models\User;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkInvoicePaymentsReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = accounts::customer()->currentBranch()->get();
        $orderBookers = User::orderbookers()->currentBranch()->get();
        $payments = bulk_payments::currentBranch()->orderBy('id', 'desc')->get();

        return view('Finance.bulk_payment.index', compact('customers', 'orderBookers', 'payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $invoices = sales::where('customerID', $request->customerID)->where('orderbookerID', $request->orderbookerID)->unpaidOrPartiallyPaid()->get();
        $currencies = currencymgmt::all();

        return view('Finance.bulk_payment.create', compact('invoices', 'currencies'));
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
                    'branchID'      => auth()->user()->branchID,
                    'method' => $request->method,
                    'bank' => $request->bank,
                    'number' => $request->number,
                    'remarks' => $request->remarks,
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
                'remarks' => $request->remarks,
                'userID' => auth()->id(),
                'refID' => $ref,
                'invoiceIDs' => $saleIDs
            ]);
            createTransaction($request->customerID, $request->date,0, $net, "Bulk Payment of Inv No. $saleIDs", $ref);
            createUserTransaction(auth()->id(), $request->date,$net, 0, "Bulk Payment of Inv No. $saleIDs", $ref);
           createMethodTransaction(auth()->user()->id, $request->method, $net,0, $request->date, $request->number, $request->bank, $request->remarks, "Bulk Payment of Inv No. $saleIDs", $ref);
           if($request->method == "Cash")
           {
            createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, "Bulk Payment of Inv No. $saleIDs", $ref);
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
        return view('Finance.bulk_payment.receipt', compact('payment'));
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
}
