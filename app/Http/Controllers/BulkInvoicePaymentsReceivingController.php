<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\currencymgmt;
use App\Models\sales;
use Illuminate\Http\Request;

class BulkInvoicePaymentsReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = accounts::customer()->currentBranch()->get();

        return view('finance.bulk_payment.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $invoices = sales::where('customerID', $request->customerID)->unpaidOrPartiallyPaid()->get();
        $currencies = currencymgmt::all();

        return view('finance.bulk_payment.create', compact('invoices', 'currencies'));
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
