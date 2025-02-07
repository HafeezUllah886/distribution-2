<?php

namespace App\Http\Controllers\api;

use App\Models\accounts;
use App\Models\order_details;
use App\Models\orders;
use App\Models\products;
use App\Models\units;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\product_units;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        $from = $request->from ?? now()->toDateString();
        $to = $request->to ?? now()->toDateString();
       
        $orders = orders::with('customer', 'details.product')->where('orderbookerID', $request->user()->id)->whereBetween("date", [$from, $to])->orderBy('id', 'desc')->get();
      
        return response()->json([
            'data' => [
                'orders' => $orders,
            ]
        ], 201);
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
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'customerID' => 'required|exists:accounts,id',
                'date' => 'required|date',
                'id' => 'required|array',
                'id.*' => 'exists:products,id',
                'unit' => 'required|array',
                'unit.*' => 'exists:product_units,id',
                'pack_qty' => 'required|array',
                'pack_qty.*' => 'numeric|min:0',
                'loose_qty' => 'required|array',
                'loose_qty.*' => 'numeric|min:0',
                'price' => 'required|array',
                'price.*' => 'numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if(count($request->id) == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please select at least one product'
                ], 422);
            }

            DB::beginTransaction();
            
            $ref = getRef();
            
            $order = orders::create([
                'customerID' => $request->customerID,
                'branchID' => $request->user()->branchID,
                'orderbookerID' => $request->user()->id,
                'date' => $request->date,
                'notes' => $request->notes,
                'refID' => $ref,
            ]);

            $orderDetails = [];

            foreach($request->id as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $pc = $request->pack_qty[$key] * $unit->value;

                $orderDetail = order_details::create([
                    'orderID' => $order->id,
                    'productID' => $id,
                    'price' => $request->price[$key],
                    'pack_qty' => $request->pack_qty[$key],
                    'loose_qty' => $request->loose_qty[$key],
                    'total_pieces' => $pc + $request->loose_qty[$key],
                    'date' => $request->date,
                    'unitID' => $request->unit[$key],
                    'refID' => $ref,
                ]);

                $orderDetails[] = $orderDetail;
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully',
                'data' => [
                    'order' => $order,
                    'order_details' => $orderDetails,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
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
