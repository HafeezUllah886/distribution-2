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
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'customerID' => 'required|exists:customers,id',
                'warehouseID' => 'required|exists:warehouses,id',
                'date' => 'required|date',
                'id' => 'required|array',
                'id.*' => 'exists:products,id',
                'unit' => 'required|array',
                'unit.*' => 'exists:product_units,id',
                'qty' => 'required|array',
                'qty.*' => 'numeric|min:0',
                'bonus' => 'required|array',
                'bonus.*' => 'numeric|min:0',
                'loose' => 'required|array',
                'loose.*' => 'numeric|min:0',
                'price' => 'required|array',
                'price.*' => 'numeric|min:0',
                'discount' => 'required|array',
                'discount.*' => 'numeric|min:0',
                'discountp' => 'required|array',
                'discountp.*' => 'numeric|min:0',
                'claim' => 'required|array',
                'claim.*' => 'numeric|min:0',
                'fright' => 'required|array',
                'fright.*' => 'numeric|min:0',
                'labor' => 'required|array',
                'labor.*' => 'numeric|min:0',
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
            
            // Create sale
            $sale = sales::create([
                'customerID' => $request->customerID,
                'branchID' => auth()->user()->branchID,
                'warehouseID' => $request->warehouseID,
                'orderbookerID' => $request->orderbookerID,
                'supplymanID' => $request->supplymanID,
                'orderdate' => $request->orderdate,
                'date' => $request->date,
                'bilty' => $request->bilty,
                'transporter' => $request->transporter,
                'notes' => $request->notes,
                'refID' => $ref,
            ]);

            $total = 0;
            $totalLabor = 0;
            $saleDetails = [];

            foreach($request->id as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc = $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $frieght = $request->fright[$key];
                $discountvalue = $price * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim) + $frieght;
                $amount = $netPrice * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                $saleDetail = sale_details::create([
                    'saleID' => $sale->id,
                    'warehouseID' => $request->warehouseID,
                    'orderbookerID' => $request->orderbookerID,
                    'productID' => $id,
                    'price' => $price,
                    'discount' => $discount,
                    'discountp' => $request->discountp[$key],
                    'discountvalue' => $discountvalue,
                    'qty' => $request->qty[$key],
                    'pc' => $pc,
                    'loose' => $request->loose[$key],
                    'netprice' => $netPrice,
                    'amount' => $amount,
                    'date' => $request->date,
                    'bonus' => $request->bonus[$key],
                    'labor' => $request->labor[$key],
                    'fright' => $frieght,
                    'claim' => $claim,
                    'unitID' => $unit->id,
                    'refID' => $ref,
                ]);

                $saleDetails[] = $saleDetail;
                createStock($id, 0, $qty, $request->date, "Sold", $ref, $request->warehouseID);
            }

            // Update sale with total
            $sale->update(['net' => $total]);

            // Create transactions
            createTransaction($request->customerID, $request->date, 0, $total, "Pending Amount of Sale No. $sale->id", $ref);
            createTransaction($request->supplymanID, $request->date, $totalLabor, 0, "Labor Charges of Sale No. $sale->id", $ref);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sale created successfully',
                'data' => [
                    'sale' => $sale,
                    'sale_details' => $saleDetails,
                    'total_amount' => $total,
                    'total_labor' => $totalLabor,
                    'reference_id' => $ref
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
