<?php

namespace App\Http\Controllers;

use App\Models\purchase_order;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\products;
use App\Models\units;
use App\Models\warehouses;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? now()->toDateString();
        $end = $request->end ?? now()->toDateString();

        $orders = purchase_order::whereBetween("date", [$start, $end])->where('branchID', auth()->user()->branchID)->orderby('id', 'desc')->get();

        $vendors = accounts::vendor()->get();
        return view('purchase.index', compact('orders', 'start', 'end', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $products = products::active()->vendor($request->vendorID)->orderby('name', 'asc')->get();
        $units = units::all();
        $vendor = $request->vendorID;
        $warehouses = warehouses::currentBranch()->get();
        $unloaders = accounts::unloader()->get();
        return view('purchase.create', compact('products', 'units', 'vendor', 'warehouses', 'unloaders'));
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
    public function show(purchase_order $purchase_order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(purchase_order $purchase_order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, purchase_order $purchase_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(purchase_order $purchase_order)
    {
        //
    }
}
