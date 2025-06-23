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
        $from = $request->from ?? now()->toDateString();
        $to = $request->to ?? now()->toDateString();
        
       
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
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'discount_percentage' => $product->discountp,
                    'discount_percentage_value' => $product->discountvalue,
                    'fright' => $product->fright,
                    'delivery_charges' => $product->labor,
                    'claim' => $product->claim,
                    'net_price' => $product->netprice,
                    'amount' => $product->amount,
                ];
            }

            $orders[] = [
                'order_id' => $order->id,
                'date' => $order->date,
                'net' => $order->net,
                'status' => $order->status,
                'notes' => $order->notes,
                'branch' => $order->branch->name,
                'customer' => ['title' => $order->customer->title, 'area' => $order->customer->area->name, 'contact' => $order->customer->contact, 'email' => $order->customer->email, 'credit_limit' => $order->customer->credit_limit],
                'products' => $orderProducts
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

                $orderDetails[] = $orderDetail;
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

        $orders = orders::where('customerID', $customer->id)
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

    
}
