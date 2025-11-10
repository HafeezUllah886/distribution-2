<?php

namespace App\Http\Controllers;

use App\Models\discountManagement;
use App\Http\Controllers\Controller;
use App\Models\area;
use App\Models\products;
use Illuminate\Http\Request;

class DiscountManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? now()->toDateString();
        $discounts = discountManagement::whereBetween('start_date', [$from, $to])->orWhereBetween('end_date', [$from, $to])->orderBy('status', 'desc')->get();
        $areas = area::currentBranch()->get();
        $products = products::currentBranch()->get();
        return view('discount_mgmt.index', compact('discounts', 'from', 'to', 'areas', 'products'));
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
        $check = discountManagement::where('customerID', $request->customer)->where('productID', $request->product)->where('status', 'Active')->first();
        if ($check) {
            return redirect()->back()->with('error', 'Discount already exists');
        }
        discountManagement::create([
            'branchID' => auth()->user()->branchID,
            'customerID' => $request->customer,
            'productID' => $request->product,
            'discount' => $request->flat_discount,
            'discountp' => $request->percentage_discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'Active',
        ]);
        return redirect()->back()->with('success', 'Discount created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(discountManagement $discountManagement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(discountManagement $discountManagement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, discountManagement $discountManagement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        discountManagement::destroy($id);

        session()->forget('confirmed_password');
        return to_route('discount.index')->with('success', 'Discount deleted successfully');
    }
}
