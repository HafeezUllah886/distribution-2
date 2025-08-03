<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\expenses;
use App\Models\order_delivery;
use App\Models\orderbooker_customers;
use App\Models\orderbooker_products;
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

        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? now()->toDateString();

        $bookerID = $request->orderbookerID ?? null;

        if($bookerID == null)
        {
            $sales = sales::with('payments')->whereBetween("date", [$start, $end])->where('branchID', auth()->user()->branchID)->orderby('id', 'desc')->get();
        }
        else
        {
            $sales = sales::with('payments')->whereBetween("date", [$start, $end])->where('branchID', auth()->user()->branchID)->where('orderbookerID', $bookerID)->orderby('id', 'desc')->get();
        }

        $warehouses = warehouses::currentBranch()->get();
        $customers = accounts::customer()->currentBranch()->get();

        $orderbookers = User::orderbookers()->currentBranch()->get();
        return view('sales.index', compact('sales', 'start', 'end', 'warehouses', 'customers', 'orderbookers', 'bookerID'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $orderbooker_products = orderbooker_products::where('orderbookerID', $request->orderbookerID)->pluck('productID')->toArray();
        $products = products::whereIn('id', $orderbooker_products)->orderby('name', 'asc')->get();
        $customer = accounts::find($request->customerID);
        foreach($products as $product)
        {
            $stock = getStock($product->id);
            $product->stock = $stock;
           
        }
        $units = units::currentBranch()->get();
        $orderbooker = User::find($request->orderbookerID);
        $warehouse = warehouses::find($request->warehouseID);
        $supplymen = accounts::supplyMen()->currentBranch()->get();
        return view('sales.create', compact('products', 'units', 'customer', 'orderbooker', 'warehouse', 'supplymen'));
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
                  'transporter'     => $request->transporter,
                  'notes'           => $request->notes,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            $customer = accounts::find($request->customerID)->title;
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
                $price_amount = $price * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                sale_details::create(
                    [
                        'saleID'        => $sale->id,
                        'warehouseID'   => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
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
                        'date'          => $request->date,
                        'bonus'         => $request->bonus[$key],
                        'labor'         => $request->labor[$key],
                        'fright'        => $request->fright[$key],
                        'claim'         => $claim,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, 0, $qty, $request->date, "Sold to $customer", $ref, $request->warehouseID);
            }

            $net = round($total,0);
            $totalLabor = round($totalLabor,0);

            $sale->update(
                [
                    'net' => $net,
                ]
            );

            createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Sale No. $sale->id", $ref, $request->orderbookerID);
            createTransaction($request->supplymanID, $request->date, 0, $totalLabor, "Labor Charges of Sale No. $sale->id Customer: $customer", $ref, $request->orderbookerID);

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

    public function showUrdu($id)
    {
        $sale = sales::findOrFail($id);
        $balance = spotBalance($sale->customerID, $sale->refID);
        return view('sales.urdu', compact('sale', 'balance'));
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
        $orderbooker_products = orderbooker_products::where('orderbookerID', $sale->orderbookerID)->pluck('productID')->toArray();
        $products = products::whereIn('id', $orderbooker_products)->orderby('name', 'asc')->get();
        $customer = accounts::find($sale->customerID);
        foreach($products as $product)
        {
            $stock = getStock($product->id);
            $product->stock = $stock ;
        }

        foreach($sale->details as $pro)
        {
            $pro->stock = round((getStock($pro->productID) + $pro->pc + $pro->bonus + $pro->loose) / $pro->unit->value);
            $pro->stock1 = round((getStock($pro->productID) + $pro->pc + $pro->bonus + $pro->loose));

        }
        $units = units::currentBranch()->get();
       
        $orderbooker = User::find($sale->orderbookerID);
        $warehouse = warehouses::find($sale->warehouseID);
        $supplymen = accounts::supplyMen()->currentBranch()->get();
        return view('sales.edit', compact('products', 'units', 'customer', 'sale', 'orderbooker', 'warehouse', 'supplymen'));
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
            /* foreach($sale->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            } */
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }

            transactions::where(['accountID' => $sale->customerID, 'refID' => $sale->refID ])->delete(); 
            transactions::where(['accountID' => $sale->supplymanID, 'refID' => $sale->refID ])->delete(); 
            $ref = $sale->refID;
           
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
        
            $sale->update(
                [
                  'orderbookerID'   => $request->orderbookerID,
                  'supplymanID'     => $request->supplymanID,
                  'orderdate'       => $request->orderdate,
                  'date'            => $request->date,
                  'bilty'           => $request->bilty,
                  'transporter'     => $request->transporter,
                  'notes'           => $request->notes,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            $customer = accounts::find($request->customerID)->title;
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
                $price_amount = $price * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                sale_details::create(
                    [
                        'saleID'        => $sale->id,
                        'warehouseID'   => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
                        'branchID'      => Auth()->user()->branchID,
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
                createStock($id, 0, $qty, $request->date, "Sold to $customer", $ref, $request->warehouseID);
            }

            $net = round($total,0);
            $totalLabor = round($totalLabor,0);

            $sale->update(
                [
                    'net' => $net,
                ]
            );

            createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Sale No. $sale->id", $ref, $sale->orderbookerID);
           
           createTransaction($request->supplymanID, $request->date, 0, $totalLabor, "Labor Charges of Sale No. $sale->id Customer: $customer", $ref, $sale->orderbookerID);

            DB::commit();
            return back()->with('success', "Sale Updated");
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
           
            $order = order_delivery::where('refID', $sale->refID)->first();
            if($order)
            {
                $order_id = $order->orderID;

                $order_status = orders::find($order_id);
                $order_status->update(
                    [
                        'status' => 'Under Process',
                    ]
                );

                order_delivery::where('refID', $sale->refID)->delete();
            }
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

    public function orderbooker_customers(Request $request)
    {
        $orderbookerID = $request->orderbookerID;
        $customerIDs = orderbooker_customers::where('orderbookerID', $orderbookerID)->pluck('customerID')->toArray();
        $accounts = accounts::whereIn('id', $customerIDs)->currentBranch()->get();
        return response()->json(
            [
                'customers' => $accounts
            ]
        );
    }
}
