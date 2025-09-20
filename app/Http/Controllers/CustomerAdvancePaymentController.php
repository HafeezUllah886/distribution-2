<?php

namespace App\Http\Controllers;

use App\Models\CustomerAdvancePayment;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\customerAdvanceConsumption;
use App\Models\method_transactions;
use App\Models\orderbooker_customers;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\transactions;
use App\Models\transactions_que;
use App\Models\User;
use App\Models\users_transactions;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class CustomerAdvancePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? date('Y-m-d');
        $orderbooker = $request->orderbooker ?? null;
    
        $advances = CustomerAdvancePayment::whereBetween('date', [$from, $to])->currentBranch();
        if ($orderbooker) {
            $advances = $advances->where('orderbookerID', $orderbooker);
        }
        $advances = $advances->orderBy('date', 'desc')->get();

        $orderbookers = User::orderbookers()->currentBranch()->get();

        $customers = accounts::customer()->currentBranch()->active()->get();

        $areas = area::currentBranch()->get();

        $currencies = currencymgmt::all();

        return view('Finance.customer_advance.index', compact('advances', 'from', 'to', 'orderbooker', 'orderbookers', 'customers', 'areas', 'currencies'));
       
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
            $payment = CustomerAdvancePayment::create(
                [
                    'customerID'       => $request->customerID,
                    'orderbookerID'    => $request->orderbookerID,
                    'date'              => $request->date,
                    'amount'            => $request->amount,
                    'method'            => $request->method,
                    'number'            => $request->number,
                    'bank'              => $request->bank,
                    'cheque_date'       => $request->cheque_date,
                    'branchID'          => $request->user()->branchID,
                    'notes'             => $request->notes,
                    'refID'             => $ref,
                ]
            );
            $depositer = accounts::find($request->customerID);
            $user_name = $request->user()->name;

           
            createTransaction($request->customerID, $request->date, 0, $request->amount, "Advance Payment deposited to $user_name : $request->notes", $ref, $request->user()->id);
            
            createMethodTransaction($request->user()->id,$request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, "Advance Payment deposited by $depositer->title : $request->notes", $ref);
    
            createUserTransaction($request->user()->id, $request->date, $request->amount, 0, "Advance Payment deposited by $depositer->title : $request->notes", $ref);
            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, "Advance Payment deposited by $depositer->title : $request->notes", $ref);
            }

            if($request->method == 'Cheque'){
                saveCheque($request->customerID, auth()->user()->id, $request->orderbookerID, $request->cheque_date, $request->amount,$request->number,$request->bank,$request->notes,$ref);
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

    /**
     * Display the specified resource.
     */
    public function show(CustomerAdvancePayment $customerAdvancePayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerAdvancePayment $customerAdvancePayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerAdvancePayment $customerAdvancePayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($ref)
    {
        try{
            DB::beginTransaction();
            customerAdvanceConsumption::where('refID', $ref)->delete();
            CustomerAdvancePayment::where('refID', $ref)->delete();
            sale_payments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('customer_advances.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('customer_advances.index')->with('error', $e->getMessage());
        }
    }

    public function pay($id)
    {
        $advance = CustomerAdvancePayment::find($id);
        $orderbookers = orderbooker_customers::where('customerID', $advance->customerID)->get();
        $customer = $advance->customer;
        return view('Finance.customer_advance.pay', compact('advance', 'orderbookers', 'customer'));
    }

    public function getBills(Request $request)
    {
        $advance = CustomerAdvancePayment::find($request->advanceID);
        $orderbooker = $request->orderbooker;
        $consumption_orderbooker = User::find($request->orderbooker);
        $customer = $advance->customer;
        $invoices = sales::where('customerID', $customer->id)->where('orderbookerID', $orderbooker)->unpaidOrPartiallyPaid()->get();
        return view('Finance.customer_advance.create', compact('advance', 'orderbooker', 'consumption_orderbooker', 'customer', 'invoices'));
    }

    public function save_consumption(Request $request)
    {

        $advance = CustomerAdvancePayment::find($request->advanceID);
        try{ 
            DB::beginTransaction();
            $ref = $advance->refID;
          
            foreach($request->invoiceID as $key => $invoiceID)
            {
                $sale = sales::find($invoiceID);
                if($request->invamount[$key] > 0)
                {
                customerAdvanceConsumption::create([
                    'customer_advanceID' => $advance->id,
                    'invoiceID' => $invoiceID,
                    'customerID' => $sale->customerID,
                    'consumption_orderbookerID' => $sale->orderbookerID,
                    'advance_orderbookerID' => $advance->orderbookerID,
                    'date' => $request->date,
                    'inv_date' => $sale->date,
                    'amount' => $request->invamount[$key],
                    'branchID' => auth()->user()->branchID,
                    'refID' => $ref
                ]);
                $saleIDs[] = $invoiceID;

                sale_payments::create([
                    'salesID' => $invoiceID,
                    'customerID' => $sale->customerID,
                    'orderbookerID' => $sale->orderbookerID,
                    'date' => $request->date,
                    'amount' => $request->invamount[$key],
                    'notes' => $request->notes,
                    'branchID' => auth()->user()->branchID,
                    'method' => "Other",
                    'bank' => "Advance",
                    'number' => "Advance",
                    'cheque_date' => $request->date,
                    'userID' => auth()->id(),
                    'refID' => $ref
                ]);
                }
            }
            $net = $request->netAmount;
            $saleIDs = implode(',', $saleIDs);

            $consumption_orderbooker = User::find($request->consumption_orderbookerID);
            $orderbooker = User::find($request->orderbookerID);

            if($request->orderbookerID   != $request->consumption_orderbookerID)
            {
                $notes_for_orderbooker = "Advance Payment of Inv No. $saleIDs from " . $sale->customer->title . " transfered to " . $consumption_orderbooker->name . " notes : " . $request->notes;
                createTransaction($sale->customerID, $request->date,$net, 0, $notes_for_orderbooker, $ref, $request->orderbookerID);

                $notes_for_consumption_orderbooker = "Advance Payment of " . $sale->customer->title . " transfered from " . $orderbooker->name . " notes : " . $request->notes;
                createTransaction($sale->customerID, $request->date,0, $net, $notes_for_consumption_orderbooker, $ref, $request->consumption_orderbookerID);
            }
           
            $consumption_notes = "Advance Payment of " . $sale->customer->title . " consumed in Inv No. $saleIDs notes : " . $request->notes;
                createTransaction($sale->customerID, $request->date,$net, $net, $consumption_notes, $ref, $request->consumption_orderbookerID);

            DB::commit();
                return to_route('customer_advances.index')->with('success', "Advance Payment Consumed");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        } 
    }
}
