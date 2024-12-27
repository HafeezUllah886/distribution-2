<?php

namespace App\Http\Controllers;

use App\Models\orderbooker_products;
use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;

class OrderbookerProductsController extends Controller
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
        $check = orderbooker_products::where('orderbookerID', $request->orderbookerID)->where('productID', $request->productID)->first();
        if($check)
        {
            return redirect()->back()->with('error', 'Product already added');
        }
        $orderbooker_products = new orderbooker_products();
        $orderbooker_products->orderbookerID = $request->orderbookerID;
        $orderbooker_products->productID = $request->productID;
        $orderbooker_products->save();
        return redirect()->back()->with('success', 'Product added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($orderbooker)
    {
        $products = products::all();
        $orderbooker_products = orderbooker_products::where('orderbookerID', $orderbooker)->get();
        $orderbooker = User::find($orderbooker);
        return view('users.products', compact('orderbooker_products', 'products', 'orderbooker'));
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
        $orderbooker_products = orderbooker_products::find($id);
        $orderbooker = $orderbooker_products->orderbookerID;
        $orderbooker_products->delete();
        session()->forget('confirmed_password');
        return to_route('orderbookerproducts.show', $orderbooker)->with('success', 'Product removed successfully');
    }

}
