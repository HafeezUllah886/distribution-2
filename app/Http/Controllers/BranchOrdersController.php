<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\order_details;
use App\Models\orders;
use App\Models\product_dc;
use App\Models\product_units;
use App\Models\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchOrdersController extends Controller
{
    public function index(request $request)
    {
        $from = $request->start ?? firstDayOfMonth();
        $to = $request->end ?? now()->toDateString();
       
        $orders = orders::with('customer.area', 'details.product', 'details.unit', 'orderbooker')->currentBranch()->whereBetween("date", [$from, $to])->orderBy('id', 'desc')->get();

        return view('orders.index', compact('orders', 'from', 'to'));
    }

    public function show($id)
    {
        $order = orders::with('customer', 'details.product', 'details.unit', 'orderbooker')->findOrFail($id);
        return view('orders.view', compact('order'));
    }

    public function edit($id)
    {
        $this->validateOrder($id);
        $products = products::all();

        $order = orders::with('customer', 'details.product', 'details.unit')->findOrFail($id);

        return view('orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, $id)
    {
        $this->validateOrder($id);
        $order = orders::findOrFail($id);
      
        try{
            DB::beginTransaction();
            $order->details()->delete();

            $validator = Validator::make($request->all(), [
                'id' => 'required|array',
                'id.*' => 'exists:products,id',
                'unit' => 'required|array',
                'unit.*' => 'exists:product_units,id',
                'qty' => 'required|array',
                'qty.*' => 'numeric|min:0',
                'loose' => 'required|array',
                'loose.*' => 'numeric|min:0',
                'bonus' => 'required|array',
                'bonus.*' => 'numeric|min:0',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }
        
            if (count($request->id) == 0) {
                throw new \Exception('At least one product must be selected.');
            }

            $net = 0;
            foreach($request->id as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $pc = $request->qty[$key] * $unit->value;

                $product = products::find($id);
                $qty = $pc + $request->loose[$key];

                $price = $product->price;
                $discount = $product->discount;
                $discountp = $product->discountp;
                $discountpValue = $discountp * $price / 100;
                $fright = $product->sfright;
                $claim = $product->sclaim;
                $dc = product_dc::where('productID', $product->id)->where('areaID', $order->customer->areaID)->first();
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
                    'qty' => $request->qty[$key],
                    'loose' => $request->loose[$key],
                    'bonus' => $request->bonus[$key],
                    'pc' => $qty,
                    'fright' => $fright,
                    'labor' => $labor,
                    'claim' => $claim,
                    'netprice' => $price - $discount - $discountpValue - $claim + $fright,
                    'amount' => $amount,
                    'date' => $order->date,
                    'unitID' => $request->unit[$key]
                ]);

            }

           if(Auth()->user()->role == "Branch Admin")
           {
            $order->update([
                'net' => $net,
                'notes' => $request->notes,
                'status' => 'Approved',
            ]);
           }
           else
           {
            $order->update([
                'net' => $net,
                'notes' => $request->notes,
                'status' => 'Pending',
            ]);
           }
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Branch.orders')->with('success', 'Order updated');
    }

    public function getSignleProduct($id, $area)
    {
        $product = products::with('units')->find($id);
        $dc = product_dc::where('productID', $product->id)->where('areaID', $area)->first();
        $product->dc = $dc->dc ?? 0;
        return $product;
    }

    private function validateOrder($id)
    {
        $order = orders::findOrFail($id);

        if($order->status == "Finalized")
        {
            return redirect()->route('orders.index')->with('error', 'Order cannot be edited');
        }

        if($order->branchID != Auth()->user()->branchID)
        {
            return redirect()->route('orders.index')->with('error', 'Order does not belong to current branch');
        }

        return true;
    }

}
