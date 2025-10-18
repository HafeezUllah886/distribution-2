<?php

namespace App\Http\Controllers;

use App\Models\purchase_order_delivery;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\expense_categories;
use App\Models\expenses;
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

        $freight_accounts = accounts::freight()->currentBranch()->get();
        $exp_categories = expense_categories::currentBranch()->get();

      return view('purchase_order.delivery', compact('order', 'warehouses', 'units', 'vendor', 'unloaders', 'freight_accounts', 'exp_categories'));
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
            $order->update([
                'status' => 'Under Process',
            ]);
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
                  'orderID'         => $order->id,
                  'transporter'     => $request->transporter,
                  'inv'             => $request->inv,
                  'status'          => "Pending",
                  'driver_name'         => $request->driver_name,
                  'driver_contact'      => $request->driver_contact,
                  'cno'                 => $request->container,
                  'freightID'           => $request->freightID,
                  'expenseCategoryID'   => $request->expense_categoryID,
                  'freight_status'      => $request->freight_status ? "Paid" : "Unpaid",
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
             $totalFreight = 0;
            $vendor = accounts::find($request->vendorID);
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
                $totalFreight += $request->fright[$key] * $pc;

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'warehouseID'   => $request->warehouseID,
                        'branchID'        => Auth()->user()->branchID,
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
                createStock($id, $qty, 0, $request->recdate, "Purchased from $vendor->title", $ref, $request->warehouseID);

                purchase_order_delivery::create(
                    [
                        'orderID'       => $request->orderID,
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouseID,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'amount'        => $amount,
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
                    'totalLabor' => $totalLabor,
                ]
            );

            $order->update([
                'status' => 'Under Process',
            ]);
            $this->checkStatus($request->orderID);


             if($request->freight_status == "on")
            {
                $vendor_title = $purchase->vendor->title;
                $fr_notes = "Freight Payment of Vendor: $vendor_title, Inv No: $request->inv, Bilty: $request->bilty, Vehicle No: $request->container, Transporter: $request->transporter, Driver:  $request->driver, Notes: $request->notes";
                expenses::create(
                    [
                        'userID'        => auth()->user()->id,
                        'amount'        => $totalFreight,
                        'branchID'      => auth()->user()->branchID,
                        'categoryID'    => $request->expense_categoryID,
                        'date'          => $request->recdate,
                        'method'        => 'Other',
                        'number'        => null,
                        'bank'          => null,
                        'cheque_date'   => $request->recdate,
                        'notes'         => $fr_notes,
                        'refID'         => $ref,
                    ]
                );

                createTransaction($request->freightID, $request->recdate, 0, $totalFreight, $fr_notes, $ref, auth()->user()->id);
    
            }

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
    public function approval($id)
    {
        try
        {
            DB::beginTransaction();
        $purchase = purchase::find($id);
        $vendor = accounts::find($purchase->vendorID);

        createTransaction($purchase->vendorID, $purchase->orderdate, 0, $purchase->net, "Pending Amount of Purchase No. $purchase->id Notes: $purchase->notes", $purchase->refID, 0);

        $unloaderNotes = "Labor Charges of Vendor: $vendor->title, Inv No. $purchase->inv  Bilty: $purchase->bilty Vehicle: $purchase->cno, Transporter: $purchase->transporter, Driver: $purchase->driver_name, Notes: $purchase->notes";
        createTransaction($purchase->unloaderID, $purchase->recdate, 0, $purchase->totalLabor, $unloaderNotes, $purchase->refID, 0);
        $purchase->update([
            'status' => 'Approved',
        ]);
        DB::commit();
        return back()->with('success', "Purchase Approved");
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
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
