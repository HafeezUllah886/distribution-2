<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\orderbooker_products;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;

class OrderbookerProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($orderbooker) {}

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
        if ($check) {
            return redirect()->back()->with('error', 'Product already added');
        }
        $orderbooker_products = new orderbooker_products;
        $orderbooker_products->orderbookerID = $request->orderbookerID;
        $orderbooker_products->productID = $request->productID;
        $orderbooker_products->save();

        $product_name = products::find($request->productID)->name;

        createNotification($request->orderbookerID, 'New Product', $product_name.' added successfully', $orderbooker_products->id);

        return redirect()->back()->with('success', 'Product added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($orderbooker, $vendor = 'All')
    {
        $orderbooker_products = orderbooker_products::where('orderbookerID', $orderbooker)->get();
        $product = $orderbooker_products->pluck('productID')->toArray();
        if ($vendor == 'All') {
            $products = products::whereNotIn('id', $product)->currentBranch()->get();
        } else {
            $products = products::where('vendorID', $vendor)->whereNotIn('id', $product)->currentBranch()->get();
        }

        $vendors = accounts::vendor()->currentBranch()->get();

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
        $product_name = products::find($orderbooker_products->productID)->name;
        $orderbooker_products->delete();
        session()->forget('confirmed_password');

        createNotification($orderbooker, 'Product Removed', $product_name.' removed successfully', $id);

        return to_route('orderbookerproducts.show', [$orderbooker, 'All'])->with('success', 'Product removed successfully');
    }
}
