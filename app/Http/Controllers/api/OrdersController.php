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
use App\Models\order_delivery;
use App\Models\product_dc;
use App\Models\product_units;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{

    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? lastDayOfMonth();
        
       
        $data = orders::with('customer.area', 'details.product', 'details.unit')->where('orderbookerID', $request->user()->id)->whereBetween("date", [$from, $to])->orderBy('id', 'desc')->get();

        $orders = [];

        foreach ($data as $order) {

            $orderProducts = [];

            // Loop through order details to get products
            foreach ($order->details as $product) {
                $orderProducts[] = [
                    'product_id' => $product->productID,
                    'product_name' => $product->product->name,
                    'product_name_urdu' => $product->product->nameurdu,
                    'unit_id' => $product->unitID, 
                    'unit_name' => $product->unit->unit_name,
                    'unit_value' => $product->unit->value, 
                    'pack_qty' => $product->qty,
                    'loose_qty' => $product->loose,
                    'bonus_qty' => $product->bonus,
                    'total_pieces' => $product->pc,
                    'price' => $product->price * $product->unit->value,
                    'discount' => round($product->discount * $product->pc, 0),
                    'discount_percentage' => round($product->discountp, 0),
                    'discount_percentage_value' => round($product->discountvalue * $product->pc, 0),
                    'fright' => $product->fright * $product->pc,
                    'delivery_charges' => $product->labor * $product->pc,
                    'claim' => $product->claim * $product->pc,
                    'net_price' => $product->netprice * $product->unit->value,
                    'amount' => $product->amount,
                ];

                $orderProducts;
            }

            $delivered_items =  $order->details()->with(['product', 'unit'])->get()->map(function($detail) {
                return [
                    'product_name' => $detail->product->name ?? null,
                    'unit_name' => $detail->unit->unit_name ?? null,
                    'pack_size' => $detail->unit->value ?? null,
                    'total_ordered' => packInfoWithOutName($detail->unit->value, $detail->pc),
                    'delivered' => packInfoWithOutName($detail->unit->value, $detail->delivered()),
                    'remaining' => packInfoWithOutName($detail->unit->value, $detail->remaining())
                ];
            });

            $orders[] = [
                'order_id' => $order->id,
                'date' => $order->date,
                'net' => $order->net,
                'status' => $order->status,
                'notes' => $order->notes,
                'branch' => $order->branch->name,
                'customer' => ['id' => $order->customerID, 'title' => $order->customer->title, 'area' => $order->customer->area->name, 'contact' => $order->customer->contact, 'email' => $order->customer->email, 'credit_limit' => $order->customer->credit_limit],
                'products' => $orderProducts,
                'delivered_items' => $delivered_items
            ];
        }
      
        return response()->json([
            'status' => 'success',
            'data' => [
                'orders' => $orders,
            ]
        ], 200);
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
                    'message' => $validator->errors(),
                ], 422);
            }

            if(count($request->id) == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please select at least one product'
                ], 422);
            }

            DB::beginTransaction();

            $customer = accounts::find($request->customerID);
            $order = orders::create([
                'customerID' => $request->customerID,
                'branchID' => $request->user()->branchID,
                'orderbookerID' => $request->user()->id,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            $orderDetails = [];
            $net = 0;
            foreach($request->id as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $pc = $request->pack_qty[$key] * $unit->value;

                $product = products::find($id);
                $qty = $pc + $request->loose_qty[$key];

                $price = $product->price;
                $discount = $product->discount;
                $discountp = $product->discountp;
                $discountpValue = $discountp * $price / 100;
                $fright = $product->sfright;
                $claim = $product->sclaim;
                $dc = product_dc::where('productID', $product->id)->where('areaID', $customer->areaID)->first();
                $labor = $dc->dc ?? 0;

                $amount = (($price - $discount - $discountpValue - $claim) + $fright) * $qty;
                $net += $amount;
            
                $orderDetail = order_details::create([
                    'orderID' => $order->id,
                    'productID' => $id,
                    'customerID' => $request->customerID,
                    'orderbookerID' => $request->user()->id,
                    'price' => $price,
                    'branchID' => $request->user()->branchID,
                    'discount' => $discount,
                    'discountp' => $discountp,
                    'discountvalue' => $discountpValue,
                    'qty' => $request->pack_qty[$key],
                    'loose' => $request->loose_qty[$key],
                    'pc' => $qty,
                    'fright' => $fright,
                    'labor' => $labor,
                    'claim' => $claim,
                    'netprice' => $price - $discount - $discountpValue - $claim + $fright,
                    'amount' => $amount,
                    'date' => $request->date,
                    'unitID' => $request->unit[$key]
                ]);

                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'customer_id' => $request->customerID,
                    'orderbooker_id' => $request->user()->id,
                    'price' => $price * $unit->value,
                    'branch_id' => $request->user()->branchID,
                    'discount' => $discount * $qty,
                    'discountp' => $discountp,
                    'discountvalue' => $discountpValue * $qty,
                    'qty' => $request->pack_qty[$key],
                    'loose' => $request->loose_qty[$key],
                    'pc' => $qty,
                    'fright' => $fright * $qty,
                    'labor' => $labor * $qty,
                    'claim' => $claim * $qty,
                    'netprice' => ($price - $discount - $discountpValue - $claim + $fright) * $unit->value,
                    'amount' => $amount,
                    'date' => $request->date,
                    'unit_id' => $request->unit[$key]
                ];
            }

            $order->update([
                'net' => round($net, 0),
            ]);
            if($net > $customer->credit_limit) {
                DB::rollback();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer credit limit exceeded'
                ], 422);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
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
                    'message' => $validator->errors(),
                ], 422);
            }

            if(count($request->id) == 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please select at least one product'
                ], 422);
            }

            DB::beginTransaction();
            $order = orders::findorFail($request->orderID);

            $this->validateOrder($request->orderID, $request->user()->id);

            $order->details()->delete();

            $customer = accounts::find($order->customerID);
            $order->update([
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            $orderDetails = [];
            $net = 0;
            foreach($request->id as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $pc = $request->pack_qty[$key] * $unit->value;

                $product = products::find($id);
                $qty = $pc + $request->loose_qty[$key];

                $price = $product->price;
                $discount = $product->discount;
                $discountp = $product->discountp;
                $discountpValue = $discountp * $price / 100;
                $fright = $product->sfright;
                $claim = $product->sclaim;
                $dc = product_dc::where('productID', $product->id)->where('areaID', $customer->areaID)->first();
                $labor = $dc->dc ?? 0;

                $amount = (($price - $discount - $discountpValue - $claim) + $fright) * $qty;
                $net += $amount;
            
                $orderDetail = order_details::create([
                    'orderID' => $order->id,
                    'productID' => $id,
                    'customerID' => $order->customerID,
                    'orderbookerID' => $order->orderbookerID,
                    'branchID' => $order->branchID,
                    'price' => $price,
                    'discount' => $discount,
                    'discountp' => $discountp,
                    'discountvalue' => $discountpValue,
                    'qty' => $request->pack_qty[$key],
                    'loose' => $request->loose_qty[$key],
                    'pc' => $qty,
                    'fright' => $fright,
                    'labor' => $labor,
                    'claim' => $claim,
                    'netprice' => $price - $discount - $discountpValue - $claim + $fright,
                    'amount' => $amount,
                    'date' => $request->date,
                    'unitID' => $request->unit[$key]
                ]);

                $orderDetails[] = $orderDetail;
            }

            $order->update([
                'net' => $net,
            ]);
            if($net > $customer->credit_limit) {
                $order->delete(); 
                DB::rollback();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer credit limit exceeded'
                ], 422);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order Updated successfully',
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        
        try
        {
            $this->validateOrder($request->order_id, $request->user()->id);
            DB::beginTransaction();
            $order = orders::find($request->order_id);
            $order->details()->delete();
            $order->delete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Order deleted successfully',
            ], 201);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function validateOrder($id, $orderbooker)
    {
        $order = orders::findOrFail($id);

        if (in_array($order->status, ["Finalized", "Approved"])) {
            throw new Exception('Order Already Approved / Finalized');
        }
    
        if ($order->orderbookerID != $orderbooker) {
            throw new Exception('This order does not belong to you');
        }

        return true;  
    }


    public function stock(Request $request)
    {
        $user = $request->user();
        $product = products::find($request->productID);
        return response()->json([
            'status' => 'success',
            'message' => 'Stock retrieved successfully',
            'data' => [
                'stock' => packInfo($product->units[0]->value, $product->units[0]->unit_name, getBranchProductStock($request->productID, $user->branchID)),
            ]
        ], 200);
    }

    public function pendingQty(Request $request)
    {
        $user = $request->user();
        $product = products::find($request->productID);
        $customer = accounts::find($request->customerID);

        $orders = orders::where('customerID', $customer->id)->where('orderbookerID', $user->id)
            ->where('status', '!=', 'Completed')
            ->pluck('id')->toArray();

        $orderQty = order_details::whereIn('orderID', $orders)
            ->where('productID', $product->id)
            ->sum('pc');

            $deliveredQty = order_delivery::whereIn('orderID', $orders)
                ->where('productID', $product->id)
                ->sum('pc');

                $pendingQty = $orderQty - $deliveredQty;

        return response()->json([
            'status' => 'success',
            'message' => 'Pending quantity retrieved successfully',
            'data' => [
                'pending_qty' => packInfo($product->units[0]->value, $product->units[0]->unit_name, $pendingQty),
            ]
        ], 200);
    }

    public function show(Request $request)
    {
        $order = orders::with('details.product')->find($request->orderID);

        $data = [
            'id' => $order->id,
            'date' => $order->date,
            'customer' => $order->customer->title,
            'customerID' => $order->customerID,
            'orderbooker' => $order->orderbooker->title,
            'orderbookerID' => $order->orderbookerID,
            'orderdate' => $order->orderdate,
            'bilty' => $order->bilty,
            'transporter' => $order->transporter,
            'net' => $order->net,
            'status' => $order->status,
            'notes' => $order->notes,
            'products' => $order->details()->with(['product', 'unit'])->get()->map(function($detail) {
                return [
                    'product_name' => $detail->product->name ?? null,
                    'unit_name' => $detail->unit->unit_name ?? null,
                    'pack_size' => $detail->unit->value ?? null,
                    'pack_qty' => $detail->qty,
                    'loose' => $detail->loose,
                    'bonus' => $detail->bonus,
                    'price' => $detail->price,
                    'discount' => $detail->discount,
                    'discount_percentage' => $detail->discountp,
                    'discount_percentage_value' => $detail->discountvalue,
                    'fright' => $detail->fright,
                    'labor' => $detail->labor,
                    'claim' => $detail->claim,
                    'net_price' => $detail->netprice,
                    'amount' => $detail->amount
                ];
            }),
            'delivered_items' => $order->details()->with(['product', 'unit'])->get()->map(function($detail) {
                return [
                    'product_name' => $detail->product->name ?? null,
                    'unit_name' => $detail->unit->unit_name ?? null,
                    'pack_size' => $detail->unit->value ?? null,
                    'total_ordered' => packInfoWithOutName($detail->unit->value, $detail->pc),
                    'delivered' => packInfoWithOutName($detail->unit->value, $detail->delivered()),
                    'remaining' => packInfoWithOutName($detail->unit->value, $detail->remaining())
                ];
            }),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Order retrieved successfully',
            'data' => $data
        ], 200);
    }

    
}
