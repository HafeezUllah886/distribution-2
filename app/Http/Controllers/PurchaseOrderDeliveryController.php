<?php

namespace App\Http\Controllers;

use App\Models\purchase_order_delivery;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orders;
use App\Models\product_units;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_order;
use App\Models\units;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($orderID)
    {
        $order = purchase_order::findOrFail($orderID);
      $warehouses = warehouses::currentBranch()->get();
      $units = units::all();
      $vendor = $order->vendor;
      $unloaders = accounts::unloader()->get();

      foreach ($order->details as $product) {
        $product->delivered = $product->delivered();
        $product->remaining = $product->remaining();
        $product->unit_value = $product->unit->value;
        $product->unit_name = $product->unit->unit_name;
      }

      return view('purchase_order.delivery', compact('order', 'warehouses', 'units', 'vendor', 'unloaders'));
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
            $order = purchase_order::find($request->orderID);
            $purchase = purchase::create(
                [
                  'vendorID'        => $request->vendorID,
                  'branchID'        => Auth()->user()->branchID,
                  'warehouseID'     => $request->warehouseID,
                  'unloaderID'      => $request->unloaderID,
                  'orderdate'       => $order->date,
                  'recdate'         => $request->recdate,
                  'notes'           => $request->notes,
                  'bilty'           => $request->bilty,
                  'transporter'     => $request->transporter,
                  'inv'             => $request->inv,
                  'status'          => "Pending",
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                
                if($qty > 0)
                {
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

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'warehouseID'   => $request->warehouseID,
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
                        'date'          => $request->recdate,
                        'bonus'         => $request->bonus[$key],
                        'labor'         => $request->labor[$key],
                        'fright'        => $request->fright[$key],
                        'claim'         => $claim,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, $qty, 0, $request->recdate, "Purchased", $ref, $request->warehouseID);

                purchase_order_delivery::create(
                    [
                        'orderID'       => $request->orderID,
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouseID,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
            }
            }

            $net = $total;

            $purchase->update(
                [
                    'net' => $net,
                ]
            );

            $order->update([
                'status' => 'Under Process',
            ]);

            DB::commit();
            return back()->with('success', "Purchase Created");
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
    public function show(purchase_order_delivery $purchase_order_delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(purchase_order_delivery $purchase_order_delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, purchase_order_delivery $purchase_order_delivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(purchase_order_delivery $purchase_order_delivery)
    {
        //
    }

    
}
