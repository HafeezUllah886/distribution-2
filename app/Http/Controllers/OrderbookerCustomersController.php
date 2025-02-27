<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\User;
use Illuminate\Http\Request;

class OrderbookerCustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($orderbooker)
    {

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
        $check = orderbooker_customers::where('orderbookerID', $request->orderbookerID)->where('customerID', $request->customerID)->first();
        if($check)
        {
            return redirect()->back()->with('error', 'Customer already added');
        }
        $orderbooker_customers = new orderbooker_customers();
        $orderbooker_customers->orderbookerID = $request->orderbookerID;
        $orderbooker_customers->customerID = $request->customerID;
        $orderbooker_customers->save();
        return redirect()->back()->with('success', 'Customer added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($orderbooker)
    {
        $customers = accounts::customer()->currentBranch()->get();
        $orderbooker_customers = orderbooker_customers::where('orderbookerID', $orderbooker)->get();
        $orderbooker = User::find($orderbooker);
        return view('users.customers', compact('orderbooker_customers', 'customers', 'orderbooker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(orderbooker_products $orderbooker_products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, orderbooker_products $orderbooker_products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $orderbooker_customers = orderbooker_customers::find($id);
        $orderbooker = $orderbooker_customers->orderbookerID;
        $orderbooker_customers->delete();
        session()->forget('confirmed_password');
        return to_route('orderbookercustomers.show', $orderbooker)->with('success', 'Customer removed successfully');
    }

}
