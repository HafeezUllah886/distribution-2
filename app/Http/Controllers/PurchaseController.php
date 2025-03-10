<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\product_units;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_payments;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\warehouses;
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
        $start = $request->start ?? now()->toDateString();
        $end = $request->end ?? now()->toDateString();

        $purchases = purchase::whereBetween("recdate", [$start, $end])->where('branchID', auth()->user()->branchID)->orderby('id', 'desc')->get();

        $vendors = accounts::vendor()->get();
        return view('purchase.index', compact('purchases', 'start', 'end', 'vendors'));
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
                  'vendorID'        => $request->vendorID,
                  'branchID'        => Auth()->user()->branchID,
                  'warehouseID'     => $request->warehouseID,
                  'unloaderID'      => $request->unloaderID,
                  'orderdate'       => $request->orderdate,
                  'recdate'         => $request->recdate,
                  'notes'           => $request->notes,
                  'bilty'           => $request->bilty,
                  'transporter'     => $request->transporter,
                  'inv'             => $request->inv,
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
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim);
                $amount = $netPrice * $pc;
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
            }

            $net = $total;

            $purchase->update(
                [
                    'net' => $net,
                ]
            );

            createTransaction($request->vendorID, $request->recdate, 0, $net, "Pending Amount of Purchase No. $purchase->id", $ref);

            createTransaction($request->unloaderID, $request->recdate, 0, $totalLabor, "Labor Charges of Purchase No. $purchase->id", $ref);
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
        $products = products::active()->vendor($purchase->vendorID)->orderby('name', 'asc')->get();
        $units = units::all();
        $accounts = accounts::business()->get();
        $warehouses = warehouses::all();
        $unloaders = accounts::unloader()->get();
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
            transactions::where('refID', $purchase->refID)->delete();
            $ref = $purchase->refID;
            $purchase->update(
                [
                  'warehouseID'     => $request->warehouseID,
                  'orderdate'       => $request->orderdate,
                  'recdate'         => $request->recdate,
                  'notes'           => $request->notes,
                  'bilty'           => $request->bilty,
                  'transporter'     => $request->transporter,
                  'inv'             => $request->inv,
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
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key] ;
                $discount = $request->discount[$key] ;
                $claim = $request->claim[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim);
                $amount = $netPrice * $pc;
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
            }

            $net = $total;

            $purchase->update(
                [
                    'net' => $net,
                ]
            );

            createTransaction($request->vendorID, $request->recdate, 0, $net, "Pending Amount of Purchase No. $purchase->id", $ref);

            createTransaction($request->unloaderID, $request->recdate, 0, $totalLabor, "Labor Charges of Purchase No. $purchase->id", $ref);
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
