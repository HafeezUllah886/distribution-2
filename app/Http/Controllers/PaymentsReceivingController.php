<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\area;
use App\Models\currency_transactions;
use App\Models\currencymgmt;
use App\Models\paymentsReceiving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsReceivingController extends Controller
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

        $payments = paymentsReceiving::currentBranch()->orderBy('id', 'desc')->whereBetween('date', [$start, $end]);
        if ($type != 'All') {
            $accounts = accounts::where('type', $type)->currentBranch()->active()->get();
            $payments = $payments->whereIn('depositerID', $accounts->pluck('id'));
            $type = [$type];
        } else {
            $type = ['Business', 'Vendor', 'Supply Man', 'Unloader', 'Customer', 'Personal', 'Investor'];
        }
        $payments = $payments->get();

        $depositers = accounts::whereIn('type', $type)->currentBranch()->active();
        if ($area != 'All') {
            $depositers = $depositers->where('areaID', $area);
        }
        $depositers = $depositers->get();

        $areas = area::currentBranch()->get();

        $currencies = currencymgmt::all();
        $type = $request->type;
        $orderbookers = User::orderbookers()->currentBranch()->active()->get();

        return view('Finance.payments_receiving.index', compact('payments', 'depositers', 'currencies', 'areas', 'type', 'area', 'orderbookers', 'start', 'end'));
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
        try {
            DB::beginTransaction();
            $ref = getRef();
            paymentsReceiving::create(
                [
                    'depositerID' => $request->depositerID,
                    'orderbookerID' => $request->orderbookerID,
                    'date' => $request->date,
                    'amount' => $request->amount,
                    'method' => $request->method,
                    'number' => $request->number,
                    'bank' => $request->bank,
                    'cheque_date' => $request->cheque_date,
                    'branchID' => auth()->user()->branchID,
                    'notes' => $request->notes,
                    'userID' => auth()->user()->id,
                    'refID' => $ref,
                ]
            );
            $depositer = accounts::find($request->depositerID);
            $user_name = auth()->user()->name;
            if ($request->method == 'Cheque') {
                $notes = "Payment deposited by $depositer->title ($depositer->type) Method $request->method Cheque Number : $request->number Bank : $request->bank Cheque Date : $request->cheque_date Notes : $request->notes";
                $notes1 = "Payment deposited to $user_name Method $request->method Cheque Number : $request->number Bank : $request->bank Cheque Date : $request->cheque_date Notes : $request->notes";
            } else {
                $notes = "Payment deposited by $depositer->title ($depositer->type) Method $request->method Notes : $request->notes";
                $notes1 = "Payment deposited to $user_name Method $request->method Notes : $request->notes";
            }
            createTransaction($request->depositerID, $request->date, 0, $request->amount, $notes1, $ref, $request->orderbookerID);

            createMethodTransaction(auth()->user()->id, $request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);

            createUserTransaction(auth()->user()->id, $request->date, $request->amount, 0, $notes, $ref);

            if ($request->method == 'Cash') {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'cr', $request->date, $notes, $ref);
            }

            if ($request->method == 'Cheque') {
                saveCheque($request->depositerID, auth()->user()->id, $request->orderbookerID, $request->cheque_date, $request->amount, $request->number, $request->bank, $request->notes, $ref);
            }

            if ($request->has('file')) {
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();

            return back()->with('success', 'Payment Saved');
        } catch (\Exception $e) {
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
        $currencies = currencymgmt::all();
        if ($payment->method == 'Cash') {

            foreach ($currencies as $currency) {
                $currenyTransaction = currency_transactions::where('currencyID', $currency->id)->where('refID', $payment->refID)->first();

                $currency->qty = $currenyTransaction->cr ?? 0;
            }

        }

        return view('Finance.payments_receiving.receipt', compact('payment', 'currencies'));
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
        $receiving = paymentsReceiving::where('refID', $ref)->first();
        $depositer = accounts::find($receiving->depositerID);
        $received_by = User::find($receiving->userID);
        $number = $receiving->number;
        $bank = $receiving->bank;
        $cheque_date = $receiving->cheque_date;
        $area = area::find($depositer->areaID)->name ?? '';

        $notes = "Payment Receiving Date: $receiving->date | Area: $area | Depositer: $depositer->title | Received By: $received_by->name | Method: $receiving->method | Bank: $bank | Number: $number | Cheque Date: $cheque_date | Amount: $receiving->amount | Notes: $receiving->notes";
        $delete = storeDeleteRequest(auth()->user()->id, $receiving->branchID, $receiving->refID, 'payment_receiving', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('payments_receiving.index')->with('success', 'Payment Receiving Delete Request Sent to Branch Admin');
    }
}
