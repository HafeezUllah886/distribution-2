<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\order_details;
use App\Models\orders;
use App\Models\products;
use App\Models\units;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{


    public function index(Request $request)
    {
        $start = $request->start ?? now()->toDateString();
        $end = $request->end ?? now()->toDateString();
        dashboard();
        if(Auth()->user()->role == "Admin")
        {
            $orders = orders::whereBetween("date", [$start, $end])->orderBy('id', 'desc')->get();
        }
        else
        {
            $orders = orders::where('orderbookerID', auth()->user()->id)->whereBetween("date", [$start, $end])->orderBy('id', 'desc')->get();
        }

        return view('orders.index', compact('orders', 'start', 'end'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::all();
        $customers = accounts::Customer()->get();
        $units = units::all();
        return view('orders.create', compact('products', 'customers', 'units'));
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
            $order = orders::create(
                [
                  'orderbookerID'  => auth()->user()->id,
                  'customerID'  => $request->customerID,
                  'date'        => $request->date,
                  'notes'       => $request->notes,
                ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $unit = units::find($request->unit[$key]);
                $product = products::find($id);
                $qty = $request->qty[$key] * $unit->value;
                $price = $product->price - $request->discount[$key];
                $amount = $qty * $price;
                $total += $amount;
                order_details::create(
                    [
                        'orderID'       => $order->id,
                        'productID'     => $id,
                        'price'         => $product->price,
                        'qty'           => $qty,
                        'discount'      => $request->discount[$key],
                        'bonus'         => $request->bonus[$key] ?? 0,
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'unitID'        => $unit->id,
                        'unitValue'     => $unit->value,
                    ]
                );
            }

            $order->update(
                [
                    'net' => $total,
                ]
            );

           DB::commit();
            return to_route('orders.show', $order->id)->with('success', "Order Created");

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
    public function show(orders $order)
    {
        return view('orders.view',compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(orders $order)
    {
        $products = products::all();
        $customers = accounts::Customer()->get();
        $units = units::all();
        return view('orders.edit', compact('products', 'customers', 'units', 'order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, orders $order)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            foreach($order->details as $product)
            {
                $product->delete();
            }
            $order->update(
                [
                  'customerID'  => $request->customerID,
                  'date'        => $request->date,
                  'notes'       => $request->notes,
                ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $unit = units::find($request->unit[$key]);
                $product = products::find($id);
                $qty = $request->qty[$key] * $unit->value;
                $price = $product->price - $request->discount[$key];
                $amount = $qty * $price;
                $total += $amount;
                order_details::create(
                    [
                        'orderID'       => $order->id,
                        'productID'     => $id,
                        'price'         => $product->price,
                        'qty'           => $qty,
                        'discount'      => $request->discount[$key],
                        'bonus'         => $request->bonus[$key],
                        'amount'        => $amount,
                        'date'          => $request->date,
                        'unitID'        => $unit->id,
                        'unitValue'     => $unit->value,
                    ]
                );
            }

            $order->update(
                [
                    'net' => $total,
                ]
            );

           DB::commit();
            return to_route('orders.show', $order->id)->with('success', "Order Update");

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
            $order = orders::find($id);

            foreach($order->details as $product)
            {
                $product->delete();
            }
            $order->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('orders.index')->with('success', "Order Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('orders.index')->with('error', $e->getMessage());
        }
    }

    public function sale($id)
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
        $order = orders::find($id);
        return view('orders.sale', compact('products', 'units', 'customers', 'accounts', 'orderbookers', 'order'));
    }
}
