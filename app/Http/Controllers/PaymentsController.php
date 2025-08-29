<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\payments;
use App\Models\staffPayments;
use App\Models\transactions;
use App\Models\transactions_que;
use App\Models\User;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
        $type = $request->type ?? 'All';
        $area = $request->area ?? 'All';

        $payments = payments::currentBranch()->whereBetween('date', [$start, $end])->orderBy('id', 'desc');
        if($type != 'All')
        {
            $accounts = accounts::where('type', $type)->currentBranch()->active()->get();
            $payments = $payments->whereIn('receiverID', $accounts->pluck('id'));
            $type = [$type];
        }
        else
        {
            $type = ['Business', 'Vendor', 'Supply Man', 'Unloader', 'Customer'];
        }
        $payments = $payments->get();

        $receivers = accounts::whereIn('type', $type)->currentBranch()->active();
        if($area != 'All')
        {
            $receivers = $receivers->where('areaID', $area);
        }
        $receivers = $receivers->get();

        $areas = area::currentBranch()->get();

        $currencies = currencymgmt::all();
        foreach($currencies as $currency)
        {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        $type = $request->type;
        $orderbookers = User::orderbookers()->currentBranch()->get();
        return view('Finance.payments.index', compact('payments', 'receivers', 'currencies', 'areas', 'type', 'area', 'orderbookers', 'start', 'end'));
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
            payments::create(
                [
                    'receiverID'    => $request->receiverID,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'cheque_date'   => $request->cheque_date,
                    'branchID'      => auth()->user()->branchID,
                    'notes'         => $request->notes,
                    'userID'        => auth()->user()->id,
                    'refID'         => $ref,
                ]
            );
            $receiver = accounts::find($request->receiverID);
            $user_name = auth()->user()->name;
            $notes = "Payment to $receiver->title Method $request->method Notes : $request->notes";

            createTransaction($request->receiverID, $request->date, $request->amount, 0, $notes, $ref, $request->orderbookerID);
            createMethodTransaction(auth()->user()->id,$request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);
           
            createUserTransaction(auth()->user()->id, $request->date,0, $request->amount, $notes, $ref);

            if($request->method == 'Cash')
            {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, $notes, $ref);
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
    public function show($id)
    {
        $payment = payments::find($id);
        $currencies = currencymgmt::all();
        if($payment->method == "Cash")
        {
          
            foreach($currencies as $currency)
            {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $payment->refID)->first();

                $currency->qty = $currenyTransaction->db ?? 0;
            }

        }
        return view('Finance.payments.receipt', compact('payment', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(vendorPayments $vendorPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, vendorPayments $vendorPayments)
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
            payments::where('refID', $ref)->delete();
            staffPayments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();

            transactions_que::where('trefID', $ref)->update([
                'status' => 'pending'
            ]);
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('payments.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('payments.index')->with('error', $e->getMessage());
        }
    }
}
