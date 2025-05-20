<?php

namespace App\Http\Controllers;

use App\Models\purchase_order;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\product_units;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_order_details;
use App\Models\units;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? now()->toDateString();
        $end = $request->end ?? now()->toDateString();
        $vendorID = $request->vendor ?? 'All';
        $status = $request->status ?? 'All';

        $orders = purchase_order::whereBetween("date", [$start, $end])->where('branchID', auth()->user()->branchID)->orderby('id', 'desc');
        if ($vendorID != 'All') {
            $orders->where('vendorID', $vendorID);
        }
        if ($status != 'All') {
            $orders->where('status', $status);
        }
        $orders = $orders->get();
        $vendors = accounts::vendor()->currentBranch()->get();
        return view('purchase_order.index', compact('orders', 'start', 'end', 'vendors', 'vendorID', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $products = products::active()->vendor($request->vendorID)->orderby('name', 'asc')->get();
        $units = units::all();
        $vendor = accounts::find($request->vendorID);
       
        return view('purchase_order.create', compact('products', 'units', 'vendor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            $ref = getRef();
            $order = purchase_order::create(
                [
                  'vendorID'        => $request->vendorID,
                  'branchID'        => Auth()->user()->branchID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
               

                purchase_order_details::create(
                    [
                        'orderID'       => $order->id,
                        'productID'     => $id,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'date'          => $request->date,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
            }

            DB::commit();
            return back()->with('success', "Purchase Order Created");
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = purchase_order::with('vendor', 'details.product', 'details.unit')->findOrFail($id);
        return view('purchase_order.view', compact('order'));
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
