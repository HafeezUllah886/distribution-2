<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\method_transactions;
use App\Models\payments;
use App\Models\paymentsReceiving;
use App\Models\transactions;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type ?? 'All';
        $area = $request->area ?? 'All';

        $payments = paymentsReceiving::currentBranch()->orderBy('id', 'desc');
        if($type != 'All')
        {
            $accounts = accounts::where('type', $type)->currentBranch()->active()->get();
            $payments = $payments->whereIn('depositerID', $accounts->pluck('id'));
            $type = [$type];
        }
        else
        {
            $type = ['Business', 'Vendor', 'Supply Man', 'Unloader', 'Customer'];
        }
        $payments = $payments->get();

        $depositers = accounts::whereIn('type', $type)->currentBranch()->active();
        if($area != 'All')
        {
            $depositers = $depositers->where('areaID', $area);
        }
        $depositers = $depositers->get();

        $areas = area::currentBranch()->get();

        $currencies = currencymgmt::all();
        $type = $request->type;
        return view('Finance.payments_receiving.index', compact('payments', 'depositers', 'currencies', 'areas', 'type', 'area'));
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
            paymentsReceiving::create(
                [
                    'depositerID'   => $request->depositerID,
                    'date'          => $request->date,
                    'amount'        => $request->amount,
                    'method'        => $request->method,
                    'number'        => $request->number,
                    'bank'          => $request->bank,
                    'remarks'       => $request->remarks,
                    'branchID'      => auth()->user()->branchID,
                    'notes'         => $request->notes,
                    'userID'        => auth()->user()->id,
                    'refID'         => $ref,
                ]
            );
            $depositer = accounts::find($request->depositerID);
            $user_name = auth()->user()->name;
            $notes = "Payment deposited by $depositer->title Method $request->method Notes : $request->notes";
            $notes1 = "Payment deposited to $user_name Method $request->method Notes : $request->notes";
            createTransaction($request->depositerID, $request->date, 0, $request->amount, $notes1, $ref);
            
            createMethodTransaction(auth()->user()->id,$request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->remarks, $notes, $ref);
    
            createUserTransaction(auth()->user()->id, $request->date, $request->amount, 0, $notes, $ref);

            if($request->method == 'Cash')
            {
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $payment = paymentsReceiving::find($id);
        return view('Finance.payments_receiving.receipt', compact('payment'));
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
            paymentsReceiving::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('payments_receiving.index')->with('success', "Payment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('payments_receiving.index')->with('error', $e->getMessage());
        }
    }
}
