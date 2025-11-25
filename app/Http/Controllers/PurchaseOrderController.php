<?php

namespace App\Http\Controllers;

use App\Models\purchase_order;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\expenses;
use App\Models\product_units;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_order_delivery;
use App\Models\purchase_order_details;
use App\Models\units;
use App\Models\warehouses;
use App\purchaseOrderTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{

    use purchaseOrderTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
        $vendorID = $request->vendorID ?? 'All';
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
                  'bilty'           => $request->bilty,
                  'vehicle'         => $request->vehicle,
                  'driver_name'     => $request->driver,
                  'driver_contact'  => $request->driver_contact,
                  'transporter'     => $request->transporter,
                  'inv'             => $request->inv,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;
            $total = 0;
            $totalLabor = 0;
            $totalFreight = 0;

            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim);
                $amount = $netPrice * $pc;
                $price_amount = $price * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;
                 $totalFreight += $request->fright[$key] * $pc;

                purchase_order_details::create(
                    [
                        'orderID'       => $order->id,
                        'productID'     => $id,
                        'price'         => $price,
                        'discount'      => $discount,
                        'discountp'     => $request->discountp[$key],
                        'discountvalue' => $discountvalue,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'netprice'      => $netPrice,
                        'amount'        => $amount,
                        'price_amount'  => $price_amount,
                        'date'          => $request->date,
                        'bonus'         => $request->bonus[$key],
                        'labor'         => $request->labor[$key],
                        'fright'        => $request->fright[$key],
                        'claim'         => $claim,
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
        $this->checkStatus($id);
        $order = purchase_order::with('vendor', 'details.product', 'details.unit')->findOrFail($id);
        return view('purchase_order.view', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->checkStatus($id);
        $this->validateOrder($id);
        $order = purchase_order::with('vendor', 'details.product', 'details.unit')->findOrFail($id);
        $products = products::active()->vendor($order->vendorID)->orderby('name', 'asc')->get();

        foreach($order->details as $product)
        {
            $received = purchase_order_delivery::where('orderID', $id)->where('productID', $product->productID)->sum('pc');
            $product->received = $received;
        }
        

        $units = units::all();
        $vendor = accounts::find($order->vendorID);
        return view('purchase_order.edit', compact('order', 'products', 'units', 'vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->checkStatus($id);
        $this->validateOrder($id);

        try
        {
            DB::beginTransaction();

            $order = purchase_order::findOrFail($id);
            $order->details()->delete();

            $order->update([
                'vendorID'        => $request->vendorID,
                'date'            => $request->date,
                'notes'           => $request->notes,
                'bilty'           => $request->bilty,
                'vehicle'         => $request->vehicle,
                'driver_contact'  => $request->driver_contact,
                'transporter'     => $request->transporter,
                'inv'             => $request->inv,
            ]);

            $ids = $request->id;
            $total = 0;
            $totalLabor = 0;

            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim);
                $amount = $netPrice * $pc;
                $price_amount = $price * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                purchase_order_details::create(
                    [
                        'orderID'       => $order->id,
                        'productID'     => $id,
                        'price'         => $price,
                        'discount'      => $discount,
                        'discountp'     => $request->discountp[$key],
                        'discountvalue' => $discountvalue,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'netprice'      => $netPrice,
                        'amount'        => $amount,
                        'price_amount'  => $price_amount,
                        'date'          => $request->date,
                        'bonus'         => $request->bonus[$key],
                        'labor'         => $request->labor[$key],
                        'fright'        => $request->fright[$key],
                        'claim'         => $claim,
                        'unitID'        => $unit->id,
                        'refID'         => $order->refID,
                    ]
                );
            }

            DB::commit();
            return back()->with('success', "Purchase Order Updated");
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try
        {
            $order = purchase_order::findOrFail($id);
            $order->details()->delete();
            $order->delete();
            session()->forget('confirmed_password');
            return to_route('purchase_order.index')->with('success', "Purchase Order Deleted");
        }
        catch(\Exception $e)
        {
            session()->forget('confirmed_password');
            return to_route('purchase_order.index')->with('error', $e->getMessage());
        }
    }

    public function validateOrder($id)
    {
        $order = purchase_order::findOrFail($id);

     /*    if($order->status != "Pending")
        {
            return redirect()->route('purchase_order.index')->with('error', 'Order cannot be edited');
        } */

        if($order->branchID != Auth()->user()->branchID)
        {
            return redirect()->route('purchase_order.index')->with('error', 'Order does not belong to current branch');
        }

        return true;
    }

    public function checkStatus($orderID)
    {
        $order = purchase_order::findOrFail($orderID);
       
            $order_pc = $order->details->sum('pc');
            $delivered_pc = $order->delivered_items->sum('pc');

            if($order_pc == $delivered_pc)
            {
                $order->status = "Completed";
              
            }

            if($order_pc > $delivered_pc)
            {
                $order->status = "Under Process";
               
            }
            if($delivered_pc == 0)
            {
                $order->status = "Pending";
               
            }
            $order->save();
    }


}
