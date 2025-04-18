<?php

namespace App\Http\Controllers;

use App\Models\orderbooker_products;
use App\Http\Controllers\Controller;
use App\Models\accounts;
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
    public function show($orderbooker, $vendor = "All")
    {
        $orderbooker_products = orderbooker_products::where('orderbookerID', $orderbooker)->get();
        $product = $orderbooker_products->pluck('productID')->toArray();
        if($vendor == "All")
        {
            $products = products::whereNotIn('id', $product)->get();
        }
        else
        {
            $products = products::where('vendorID', $vendor)->whereNotIn('id', $product)->get();
        }

        $vendors = accounts::vendor()->get();
        
        
        $orderbooker = User::find($orderbooker);
        return view('users.products', compact('orderbooker_products', 'products', 'orderbooker', 'vendors', 'vendor'));
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
        return to_route('orderbookerproducts.show', [$orderbooker, 'All'])->with('success', 'Product removed successfully');
    }

}
