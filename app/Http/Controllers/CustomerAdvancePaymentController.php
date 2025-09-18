<?php

namespace App\Http\Controllers;

use App\Models\CustomerAdvancePayment;
use App\Http\Controllers\Controller;
use App\Models\orderbooker_customers;
use App\Models\sales;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view('Finance.customer_advance.index', compact('advances', 'from', 'to', 'orderbooker', 'orderbookers'));
       
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
        //
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
    public function destroy(CustomerAdvancePayment $customerAdvancePayment)
    {
        //
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
        $customer = $advance->customer;
        $invoices = sales::where('customerID', $customer->id)->where('orderbookerID', $orderbooker)->unpaidOrPartiallyPaid()->get();
        return view('Finance.customer_advance.create', compact('advance', 'orderbooker', 'customer', 'invoices'));
    }
}
