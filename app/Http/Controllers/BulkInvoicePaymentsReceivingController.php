<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currencymgmt;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\User;
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

        return view('finance.bulk_payment.index', compact('customers', 'orderBookers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $invoices = sales::where('customerID', $request->customerID)->where('orderbookerID', $request->orderbookerID)->unpaidOrPartiallyPaid()->get();
        $currencies = currencymgmt::all();

        return view('finance.bulk_payment.create', compact('invoices', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{ 
            DB::beginTransaction();
            $ref = getRef();
           
            foreach($request->invoiceID as $key => $invoiceID)
            {
                $sale = sales::find($invoiceID);
                sale_payments::create([
                    'salesID' => $invoiceID,
                    'date' => $request->date,
                    'amount' => $request->invamount[$key],
                    'notes' => $request->notes,
                    'userID' => auth()->id(),
                    'refID' => $ref
                ]);
            }
            $total = array_sum($request->invamount);
            $saleIDs = implode(',', $request->invoiceID);
            createTransaction($request->customerID, $request->date,0, $total, "Bulk Payment of Inv No. $saleIDs", $ref);
            createUserTransaction(auth()->id(), $request->date,$total, 0, "Bulk Payment of Inv No. $saleIDs", $ref);
            createCurrencyTransaction(auth()->id(), $request->currencyID, $request->qty, 'cr', $request->date, "Bulk Payment of Inv No. $saleIDs", $ref);
            DB::commit();
                return back()->with('success', "Bulk Payment Saved");
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
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
