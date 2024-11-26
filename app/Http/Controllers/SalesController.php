<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\orders;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\salesman;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $start = $request->start ?? now()->toDateString();
        $end = $request->end ?? now()->toDateString();

        $sales = sales::with('payments')->whereBetween("date", [$start, $end])->orderby('id', 'desc')->get();
        return view('sales.index', compact('sales', 'start', 'end'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::orderby('name', 'asc')->get();
        foreach($products as $product)
        {
            $stock = getStock($product->id);
            $product->stock = $stock;
        }
        $units = units::all();
        $customers = accounts::customer()->get();
        $accounts = accounts::business()->get();
        $orderbookers = User::where('role', 'Orderbooker')->get();
        return view('sales.create', compact('products', 'units', 'customers', 'accounts', 'orderbookers'));
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
            $sale = sales::create(
                [
                  'customerID'  => $request->customerID,
                  'date'        => $request->date,
                  'notes'       => $request->notes,
                  'discount'    => $request->discount1,
                  'fright'      => $request->fright,
                  'fright1'      => $request->fright1,
                  'wh'          => $request->whTax,
                  'orderbookerID'  => $request->orderbookerID,
                  'refID'       => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $unit = units::find($request->unit[$key]);
                $qty1 = ($request->qty[$key] * $unit->value) + $request->bonus[$key];
                $qty = $request->qty[$key] * $unit->value;
                $price = $request->price[$key];
                $total += $request->ti[$key];
                sale_details::create(
                    [
                        'salesID'       => $sale->id,
                        'productID'     => $id,
                        'price'         => $price,
                        'qty'           => $qty,
                        'discount'      => $request->discount[$key],
                        'ti'            => $request->ti[$key],
                        'tp'            => $request->tp[$key],
                        'gst'           => $request->gst[$key],
                        'gstValue'      => $request->gstValue[$key],
                        'date'          => $request->date,
                        'bonus'         => $request->bonus[$key],
                        'unitID'        => $unit->id,
                        'unitValue'     => $unit->value,
                        'refID'         => $ref,
                    ]
                );
                createStock($id,0, $qty1, $request->date, "Sold in Inv # $sale->id", $ref);
            }

            $whTax = $total * $request->whTax / 100;

            $net = ($total + $whTax + $request->fright1) - ($request->discount1 + $request->fright);

            $sale->update(
                [

                    'whValue'   => $whTax,
                    'net'       => $net,
                ]
            );

            if($request->status == 'paid')
            {
                sale_payments::create(
                    [
                        'salesID'       => $sale->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $ref,
                    ]
                );

                createTransaction($request->accountID, $request->date, $net, 0, "Payment of Inv No. $sale->id", $ref);
                createTransaction($request->customerID, $request->date, $net, $net, "Payment of Inv No. $sale->id", $ref);
            }
            else
            {
                createTransaction($request->customerID, $request->date, 0, $net, "Pending Amount of Inv No. $sale->id", $ref);
            }

            if($request->orderID)
            {
                $order = orders::find($request->orderID);
                $order->update(
                    [
                        'saleID' => $sale->id,
                        'status' => "Completed",
                    ]
                );
            }

          DB::commit();
            return to_route('sale.show', $sale->id)->with('success', "Sale Created");

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
    public function show(sales $sale)
    {
        $balance = spotBalance($sale->customerID, $sale->refID);
        return view('sales.view', compact('sale', 'balance'));
    }

    public function gatePass($id)
    {
        $sale = sales::find($id);
        return view('sales.gatepass', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sales $sale)
    {
        $products = products::orderby('name', 'asc')->get();
        $units = units::all();
        $customers = accounts::customer()->get();
        $accounts = accounts::business()->get();
        $orderbookers = User::where('role', 'Orderbooker')->get();
        return view('sales.edit', compact('products', 'units', 'customers', 'accounts', 'sale', 'orderbookers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try
        {
            DB::beginTransaction();
            $sale = sales::find($id);
            foreach($sale->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $sale->refID)->delete();
            $ref = $sale->refID;
            $sale->update(
                [
                    'customerID'  => $request->customerID,
                    'date'        => $request->date,
                    'notes'       => $request->notes,
                    'discount'    => $request->discount1,
                    'fright'      => $request->fright,
                    'fright1'      => $request->fright1,
                    'wh'          => $request->whTax,
                    'orderbookerID'  => $request->orderbookerID,
                    'refID'       => $ref,
                  ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $unit = units::find($request->unit[$key]);
                $qty = $request->qty[$key] * $unit->value;
                $price = $request->price[$key];
                $total += $request->ti[$key];
                sale_details::create(
                    [
                        'salesID'       => $sale->id,
                        'productID'     => $id,
                        'price'         => $price,
                        'qty'           => $qty,
                        'discount'      => $request->discount[$key],
                        'ti'            => $request->ti[$key],
                        'tp'            => $request->tp[$key],
                        'gst'           => $request->gst[$key],
                        'gstValue'      => $request->gstValue[$key],
                        'date'          => $request->date,
                        'unitID'        => $unit->id,
                        'unitValue'     => $unit->value,
                        'refID'         => $sale->refID,
                    ]
                );
                createStock($id,0, $qty, $request->date, "Sold in Inv # $sale->id", $sale->refID);
            }

            $whTax = $total * $request->whTax / 100;

            $net = ($total + $whTax + $request->fright1) - ($request->discount1 + $request->fright);
            dashboard();
            $sale->update(
                [

                    'whValue'   => $whTax,
                    'net'       => $net,
                ]
            );

            if($request->status == 'paid')
            {
                sale_payments::create(
                    [
                        'salesID'       => $sale->id,
                        'accountID'     => $request->accountID,
                        'date'          => $request->date,
                        'amount'        => $net,
                        'notes'         => "Full Paid",
                        'refID'         => $sale->refID,
                    ]
                );
                createTransaction($request->accountID, $request->date, $net, 0, "Payment of Inv No. $sale->id", $sale->refID);
                createTransaction($request->customerID, $request->date, $net, $net, "Payment of Inv No. $sale->id", $ref);
            }
            else
            {
                createTransaction($request->customerID, $request->date, 0, $net, "Pending Amount of Inv No. $sale->id", $sale->refID);
            }
            DB::commit();
            return to_route('sale.index')->with('success', "Sale Updated");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return to_route('sale.index')->with('error', $e->getMessage());
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
            $sale = sales::find($id);
            foreach($sale->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            }
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $sale->refID)->delete();
            $sale->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('sale.index')->with('success', "Sale Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('sale.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = products::with('unit')->find($id);
        $stocks = stock::select(DB::raw('SUM(cr) - SUM(db) AS balance'))
                  ->where('productID', $product->id)
                  ->get();

        $product->stock = getStock($id);
        return $product;
    }

    public function getProductByCode($code)
    {
        $product = products::where('code', $code)->first();
        if($product)
        {
           return $product->id;
        }
        return "Not Found";
    }
}
