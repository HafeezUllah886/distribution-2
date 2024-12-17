<?php

namespace App\Http\Controllers;

use App\Models\accounts;
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

        $purchases = purchase::whereBetween("recdate", [$start, $end])->orderby('id', 'desc')->get();

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
        $warehouses = warehouses::all();
        $accounts = accounts::business()->get();
        return view('purchase.create', compact('products', 'units', 'vendor', 'accounts', 'warehouses'));
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
            foreach($ids as $key => $id)
            {
                $unit = units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key];
                $pc = $request->qty[$key] * $unit->value;
                $price = $request->price[$key] ;
                $discount = $request->discount[$key] ;
                $claim = $request->claim[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim);
                $amount = $netPrice * $request->qty[$key];
                $total += $amount;

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'price'         => $price,
                        'discount'      => $discount,
                        'discountp'     => $request->discountp[$key],
                        'discountvalue' => $discountvalue,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
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
        $products = products::orderby('name', 'asc')->get();
        $units = units::all();
        $vendors = accounts::vendor()->get();
        $accounts = accounts::business()->get();
        return view('purchase.edit', compact('products', 'units', 'vendors', 'accounts', 'purchase'));
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
            foreach($purchase->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();

            $purchase->update(
                [
                    'vendorID'        => $request->vendorID,
                    'date'            => $request->date,
                    'notes'           => $request->notes,
                    'discount'        => $request->discount,
                    'fright'          => $request->fright,
                    'fright1'          => $request->fright1,
                    'wh'              => $request->whTax,
                    'inv'             => $request->inv,
                  ]
            );

            $ids = $request->id;

            $total = 0;
            $ref = $purchase->refID;
            dashboard();
            foreach($ids as $key => $id)
            {
                $unit = units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key];
                $qty1 = $request->qty[$key] * $unit->value;
                $pprice = $request->pprice[$key];
                $price = $request->price[$key];
                $wsprice = $request->wsprice[$key];
                $tp = $request->tp[$key];
                $amount = $pprice * $qty1;
                $total += $amount;

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'pprice'        => $pprice,
                        'price'         => $price,
                        'wsprice'       => $wsprice,
                        'tp'            => $tp,
                        'qty'           => $qty1,
                        'gstValue'      => $request->gstValue[$key],
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'bonus'         => $request->bonus[$key],
                        'unitID'        => $unit->id,
                        'unitValue'     => $unit->value,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, $qty, 0, $request->date, "Purchased", $ref);

                $product = products::find($id);
                $product->update(
                    [
                        'pprice' => $pprice,
                        'price'  => $price,
                        'wsprice' => $wsprice,
                    ]
                );
            }

            $whTax = $total * $request->whTax / 100;

            $net = ($total + $whTax + $request->fright1) - ($request->discount + $request->fright);

            $purchase->update(
                [

                    'whValue'   => $whTax,
                    'net'       => $net,
                ]
            );

            if($request->status == 'paid')
            {
                purchase_payments::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );

                createTransaction($request->accountID, $request->date, 0, $net, "Payment of Purchase No. $purchase->id", $ref);
            }
            else
            {
                createTransaction($request->vendorID, $request->date, 0, $net, "Pending Amount of Purchase No. $purchase->id", $ref);
            }
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
            foreach($purchase->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
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
