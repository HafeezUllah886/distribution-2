<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\expenses;
use App\Models\orders;
use App\Models\product_dc;
use App\Models\product_units;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\salesman;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\User;
use App\Models\warehouses;
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

        $warehouses = warehouses::currentBranch()->get();
        $customers = accounts::customer()->currentBranch()->get();
        return view('sales.index', compact('sales', 'start', 'end', 'warehouses', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $products = products::orderby('name', 'asc')->get();
        $customer = accounts::find($request->customerID);
        foreach($products as $product)
        {
            $stock = getStock($product->id);
            $product->stock = $stock;
           
        }
        $units = units::all();
       
        $accounts = accounts::business()->get();
        $orderbookers = User::orderbookers()->get();
        $warehouse = warehouses::find($request->warehouseID);
        $supplymen = accounts::supplyMen()->get();
        return view('sales.create', compact('products', 'units', 'customer', 'accounts', 'orderbookers', 'warehouse', 'supplymen'));
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
                  'customerID'      => $request->customerID,
                  'branchID'        => Auth()->user()->branchID,
                  'warehouseID'     => $request->warehouseID,
                  'orderbookerID'   => $request->orderbookerID,
                  'supplymanID'     => $request->supplymanID,
                  'orderdate'       => $request->orderdate,
                  'date'            => $request->date,
                  'bilty'           => $request->bilty,
                  'transporter'           => $request->transporter,
                  'notes'           => $request->notes,
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
                $frieght = $request->fright[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim) + $frieght;
                $amount = $netPrice * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                sale_details::create(
                    [
                        'saleID'        => $sale->id,
                        'warehouseID'   => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
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
                        'date'          => $request->date,
                        'bonus'         => $request->bonus[$key],
                        'labor'         => $request->labor[$key],
                        'fright'        => $request->fright[$key],
                        'claim'         => $claim,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, 0, $qty, $request->date, "Sold", $ref, $request->warehouseID);
            }

            $net = $total;

            $sale->update(
                [
                    'net' => $net,
                ]
            );

            createTransaction($request->customerID, $request->date, 0, $net, "Pending Amount of Sale No. $sale->id", $ref);
           
            createTransaction($request->supplymanID, $request->date, $totalLabor, 0, "Labor Charges of Sale No. $sale->id", $ref);

            DB::commit();
            return back()->with('success', "Sale Created");
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

    public function getSignleProduct($id, $warehouse, $area)
    {
        $product = products::with('units')->find($id);
        $stocks = stock::select(DB::raw('SUM(cr) - SUM(db) AS balance'))
                  ->where('productID', $product->id)
                  ->get();
        $product->stock = getWarehouseProductStock($id, $warehouse);
        $dc = product_dc::where('productID', $product->id)->where('areaID', $area)->first();
        $product->dc = $dc->dc ?? 0;
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
