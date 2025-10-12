<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\product_units;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_order;
use App\Models\purchase_order_delivery;
use App\Models\purchase_payments;
use App\Models\expenses;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\warehouses;
use App\Models\expense_categories;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
        $vendorID = $request->vendorID ?? 'All';
        $status = $request->status ?? 'All';

        $purchases = purchase::whereBetween("recdate", [$start, $end])->where('branchID', auth()->user()->branchID);
        if ($vendorID != 'All') {
            $purchases->where('vendorID', $vendorID);
        }
        if ($status != 'All') {
            $purchases->where('status', $status);
        }
        $purchases = $purchases->orderby('id', 'desc')->get();
        $vendors = accounts::vendor()->currentBranch()->get();
      
        return view('purchase.index', compact('purchases', 'start', 'end', 'vendors', 'vendorID', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $products = products::active()->vendor($request->vendorID)->orderby('name', 'asc')->get();
        $units = units::currentBranch()->get();
        $vendor = $request->vendorID;
        $warehouses = warehouses::currentBranch()->get();
        $unloaders = accounts::unloader()->currentBranch()->get();
        $freight_accounts = accounts::freight()->currentBranch()->get();
        $exp_categories = expense_categories::currentBranch()->get();
        return view('purchase.create', compact('products', 'units', 'vendor', 'warehouses', 'unloaders', 'freight_accounts', 'exp_categories'));
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
            $purchase = purchase::create(
                [
                  'vendorID'            => $request->vendorID,
                  'branchID'            => Auth()->user()->branchID,
                  'warehouseID'         => $request->warehouseID,
                  'unloaderID'          => $request->unloaderID,
                  'orderdate'           => $request->orderdate,
                  'recdate'             => $request->recdate,
                  'notes'               => $request->notes,
                  'bilty'               => $request->bilty,
                  'transporter'         => $request->transporter,
                  'status'              => "Pending",
                  'inv'                 => $request->inv,
                  'driver_name'         => $request->driver_name,
                  'driver_contact'      => $request->driver_contact,
                  'cno'                 => $request->container,
                  'freightID'           => $request->freightID,
                  'expenseCategoryID'   => $request->expense_categoryID,
                  'freight_status'      => $request->freight_status,
                  'refID'               => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            $totalFreight = 0;
            $vendor = accounts::find($request->vendorID)->title;
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
                createStock($id, $qty, 0, $request->recdate, "Purchased from $vendor", $ref, $request->warehouseID);
            }

            $net = round($total, 0);
            $totalLabor = round($totalLabor, 0);

            $purchase->update(
                [
                    'net' => $net,
                    'totalLabor' => $totalLabor,
                ]
            );

            if($request->freight_status == "Paid")
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
    public function show(purchase $purchase)
    {
        return view('purchase.view', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(purchase $purchase)
    {
        if($purchase->orderID != null)
        {
            return back()->with('error', "This purchase can not be edited");
        }
        $products = products::active()->vendor($purchase->vendorID)->orderby('name', 'asc')->get();
        $units = units::currentBranch()->get();
        $accounts = accounts::business()->currentBranch()->get();
        $warehouses = warehouses::currentBranch()->get();
        $unloaders = accounts::unloader()->currentBranch()->get();
        return view('purchase.edit', compact('products', 'units', 'accounts', 'purchase', 'warehouses', 'unloaders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, purchase $purchase)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            if($purchase->orderID != null)
            {
                purchase_order_delivery::where('purchaseID', $purchase->id)->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();
            $ref = $purchase->refID;
            $purchase->update(
                [
                  'warehouseID'     => $request->warehouseID,
                  'orderdate'       => $request->orderdate,
                  'recdate'         => $request->recdate,
                  'notes'           => $request->notes,
                  'bilty'           => $request->bilty,
                  'status'          => "Pending",
                  'transporter'     => $request->transporter,
                  'inv'             => $request->inv,
                  'refID'           => $ref,
                  ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            $vendor = accounts::find($request->vendorID)->title;
            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key] ;
                $discount = $request->discount[$key] ;
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
                        'branchID'      => auth()->user()->branchID,
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
                createStock($id, $qty, 0, $request->recdate, "Purchased from $vendor", $ref, $request->warehouseID);

              if($purchase->orderID != null)
              {
                purchase_order_delivery::create(
                    [
                        'orderID'       => $purchase->orderID,
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'warehouseID'   => $purchase->warehouseID,
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

            $net = round($total, 0);
            $totalLabor = round($totalLabor, 0);

            $purchase->update(
                [
                    'net' => $net,
                    'totalLabor' => $totalLabor,
                ]
            );
          
            DB::commit();
            return back()->with('success', "Purchase Updated");
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
    public function destroy($id)
    {

        try
        {
            DB::beginTransaction();
            $purchase = purchase::find($id);

            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();
            purchase_order_delivery::where('purchaseID', $purchase->id)->delete();
            $purchase->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('purchase.index')->with('success', "Purchase Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('purchase.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = products::with('units')->find($id);
        return $product;
    }
}
