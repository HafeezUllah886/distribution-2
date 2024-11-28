<?php

namespace App\Http\Controllers;

use App\Models\currencymgmt;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use Illuminate\Http\Request;

class CurrencymgmtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = currencymgmt::all();

        return view('Finance.currencymgmt.index', compact('currencies'));
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
    public function show(currencymgmt $currencymgmt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(currencymgmt $currencymgmt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, currencymgmt $currencymgmt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(currencymgmt $currencymgmt)
    {
        //
    }

    public function details($accountID)
    {
        $account = accounts::find($accountID);
        $currencies = currencymgmt::all();

        return view('Finance.currencymgmt.details', compact('account', 'currencies'));
    }
}
