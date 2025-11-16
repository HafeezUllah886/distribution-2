<?php

namespace App\Http\Controllers;

use App\Models\discountManagement;
use App\Http\Controllers\Controller;
use App\Models\accounts;
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
        $area = $request->area ?? null;
        $vendor = $request->vendor ?? null;
        $status = $request->status ?? null;

        if($area != null)
        {
            $customers = accounts::currentBranch()->customer()->where('areaID', $area)->get('id')->toArray();
        }
        else
        {
            $customers = accounts::currentBranch()->customer()->get('id')->toArray();
        }

        if($vendor != null)
        {
            $products = products::currentBranch()->where('vendorID', $vendor)->get('id')->toArray();
        }
        else
        {
            $products = products::currentBranch()->get('id')->toArray();
        }


        $discounts = discountManagement::whereIn('customerID', $customers)->whereIn('productID', $products)->orderBy('status', 'desc');
        if($status != null)
        {
            $discounts = $discounts->where('status', $status);
        }
        $discounts = $discounts->get();
        $areas = area::currentBranch()->get();
        dd($areas);
        $products = products::currentBranch()->get();
        $vendors = accounts::currentBranch()->vendor()->get();
        return view('discount_mgmt.index', compact('discounts', 'areas', 'products', 'vendors', 'area', 'vendor', 'status'));
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
