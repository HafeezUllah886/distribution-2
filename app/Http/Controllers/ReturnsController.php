<?php

namespace App\Http\Controllers;

use App\Models\returns;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\orderbooker_products;
use App\Models\product_units;
use App\Models\products;
use App\Models\returnsDetails;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\stock;
use App\Models\transactions;
use App\Models\User;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? now()->toDateString();

        $bookerID = $request->orderbookerID ?? null;

        if($bookerID == null)
        {
            $returns = returns::whereBetween('date', [$start, $end])->orderBy('date', 'desc')->get();
        }
        else
        {
            $returns = returns::whereBetween('date', [$start, $end])->where('orderbookerID', $bookerID)->orderBy('date', 'desc')->get();
        }
        $customers = accounts::customer()->currentBranch()->get();
        $orderbookers = User::orderbookers()->currentBranch()->get();

        return view('return.index', compact('returns', 'start', 'end', 'customers', 'orderbookers', 'bookerID'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $warehouses = warehouses::currentBranch()->get();
        $products = orderbooker_products::where('orderbookerID', $request->orderbookerID)->get();
        $customer = accounts::find($request->customerID);
        $orderbooker = User::find($request->orderbookerID);

        $pendingInvoices = sales::where('customerID', $request->customerID)->where('orderbookerID', $request->orderbookerID)->unpaidOrPartiallyPaid()->get();

        return view('return.create', compact('warehouses', 'products', 'customer', 'pendingInvoices', 'orderbooker'));
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
            $return = returns::create(
                [
                  'customerID'      => $request->customerID,
                  'branchID'        => Auth()->user()->branchID,
                  'warehouseID'     => $request->warehouseID,
                  'orderbookerID'   => $request->orderbookerID,
                  'date'            => $request->date,
                  'invoices'        => $request->pendingInvoice,
                  'notes'           => $request->notes,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $customer = accounts::find($request->customerID);
            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $amount = $price * $pc;
                $total += $amount;

                returnsDetails::create(
                    [
                        'returnID'        => $return->id,
                        'warehouseID'   => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
                        'branchID'        => Auth()->user()->branchID,
                        'productID'     => $id,
                        'price'         => $price,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, $qty, 0, $request->date, "Returned from $customer->title", $ref, $request->warehouseID);
            }

            $net = $total;

            $return->update(
                [
                    'net' => $net,
                ]
            );

           /*  $sale = sales::find($request->pendingInvoice);
            sale_payments::create(
                [
                    'salesID'       => $sale->id,
                    'date'          => $request->date,
                    'amount'        => $net,
                    'notes'         => "Return Amount Adjusted Return No. $return->id",
                    'userID'        => auth()->id(),
                    'refID'         => $ref,
                ]
            );

            createTransaction($request->customerID, $request->date, 0, $net, "Amount of Return No. $return->id", $ref); */
           
            DB::commit();
            return back()->with('success', "Return Created");
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
    public function show(returns $return)
    {
        return view('return.view', compact('return'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $return = returns::find($id);
        $warehouses = warehouses::currentBranch()->get();
        $products = orderbooker_products::where('orderbookerID', $return->orderbookerID)->get();
        $customer = accounts::find($return->customerID);
        $orderbooker = User::find($return->orderbookerID);

        $pendingInvoices = sales::where('customerID', $return->customerID)->where('orderbookerID', $return->orderbookerID)->unpaidOrPartiallyPaid()->get();

        return view('return.edit', compact('warehouses', 'products', 'customer', 'pendingInvoices', 'orderbooker', 'return'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            $return = returns::find($id);
            
            transactions::where('refID', $return->refID)->delete();
                
            foreach($return->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            sale_payments::where('refID', $return->refID)->delete();
            $return->update(
                [
                  'customerID'      => $request->customerID,
                  'warehouseID'     => $request->warehouseID,
                  'date'            => $request->date,
                  'invoices'        => $request->pendingInvoice,
                  'notes'           => $request->notes,
                  
                ]
            );

            $ids = $request->id;

            $ref = $return->refID;

            $total = 0;
            $customer = accounts::find($request->customerID);
            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $amount = $price * $pc;
                $total += $amount;

                returnsDetails::create(
                    [
                        'returnID'        => $return->id,
                        'warehouseID'   => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
                        'productID'     => $id,
                        'price'         => $price,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, $qty, 0, $request->date, "Returned from $customer->title", $ref, $request->warehouseID);
            }

            $net = $total;
            $return->update(
                [
                    'net' => $net,
                    'status' => 1,
                ]
            );
            foreach($request->pendingInvoice as $index => $invoice)
            {
                $sale = sales::find($invoice);
                $isLast = $index === array_key_last($request->pendingInvoice);
                if($isLast)
                {
                    if($sale->due() < $net)
                    {
                        throw new Exception('Amount Exceeds');
                    }
                    sale_payments::create(
                        [
                            'salesID'       => $invoice,
                            'date'          => $request->date,
                            'amount'        => $net,
                            'notes'         => "Return Amount Adjusted Return No. $return->id",
                            'userID'        => auth()->id(),
                            'refID'         => $ref,
                        ]
                    );
                    createTransaction($request->customerID, $request->date, 0, $net, "Amount of Return No. $return->id", $ref);
                }
                else
                {
                    $amount = $sale->due();
                    $net -= $amount;
                   if($net <= 0)
                   {
                    break;
                   }
                   sale_payments::create(
                    [
                        'salesID'       => $invoice,
                        'date'          => $request->date,
                        'amount'        => $amount,
                        'notes'         => "Return Amount Adjusted Return No. $return->id",
                        'userID'        => auth()->id(),
                        'refID'         => $ref,
                    ]
                );
                createTransaction($request->customerID, $request->date, 0, $amount, "Amount of Return No. $return->id", $ref);
                }
            }
            
            DB::commit();
            return to_route('return.index')->with('success', "Return Approved");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            
            return to_route('return.index')->with('error', $e->getMessage());
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
            $return = returns::find($id);
            
            transactions::where('refID', $return->refID)->delete();
                
            foreach($return->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            sale_payments::where('refID', $return->refID)->delete();
            $return->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('return.index')->with('success', "Return Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('return.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = products::with('units')->find($id);
        return $product;
    }
}
