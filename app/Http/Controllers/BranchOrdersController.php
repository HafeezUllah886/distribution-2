<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\order_delivery;
use App\Models\order_details;
use App\Models\orders;
use App\Models\paymentsReceiving;
use App\Models\product_dc;
use App\Models\product_units;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\User;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchOrdersController extends Controller
{
    public function index(request $request)
    {
        $from = $request->start ?? firstDayOfMonth();
        $to = $request->end ?? now()->toDateString();
        $status = $request->status ?? "All";
        $bookerID = $request->orderbookerID ?? null;
       
        $orders = orders::with('customer.area', 'details.product', 'details.unit', 'orderbooker')->currentBranch()->whereBetween("date", [$from, $to])->orderBy('id', 'desc');

        if($status != "All")
        {
            $orders->where('status', $status);
        }
        if($bookerID != null)
        {
            $orders->where('orderbookerID', $bookerID);
        }
        $orders = $orders->get();

        $warehouses = warehouses::currentBranch()->get();
        $orderbookers = User::orderbookers()->currentBranch()->get();

        return view('orders.index', compact('orders', 'from', 'to', 'status', 'warehouses', 'bookerID', 'orderbookers'));
    }

    public function show($id)
    {
        $order = orders::with('customer', 'details.product', 'details.unit', 'orderbooker')->findOrFail($id);
        return view('orders.view', compact('order'));
    }

    public function edit($id)
    {
        $this->validateOrder($id);
        $products = products::currentBranch()->get();

        $order = orders::with('customer', 'details.product', 'details.unit')->findOrFail($id);

        $orderbooker = $order->orderbookerID;
        $customer = $order->customerID;
        
            $sale_payment_date = sale_payments::where('customerID', $customer)->where('orderbookerID', $orderbooker)->orderBy('id', 'desc')->pluck('date');
            $payment_receiving_date = paymentsReceiving::where('depositerID', $customer)->where('orderbookerID', $orderbooker)->orderBy('id', 'desc')->pluck('date');
        
            //newest date
            $newest_date = $sale_payment_date->max();
            if($payment_receiving_date->max() > $newest_date)
            {
                $newest_date = $payment_receiving_date->max();
            }
            
            $methods = ['Cash', 'Cheque', 'Online', 'Other'];
            $methodData = [];
            $methodData['date'] = $newest_date;
           foreach($methods as $method)
           {
            $sales_payment = sale_payments::where('customerID', $customer)->where('date', $newest_date)->where('method', $method)->sum('amount');
            $payment_receiving = paymentsReceiving::where('depositerID', $customer)->where('date', $newest_date)->where('method', $method)->sum('amount');
        
            $total = $sales_payment + $payment_receiving;
            $methodData[$method] = round($total, 2);
           }
        
           $last_sale = sales::where('customerID', $customer)->orderBy('id', 'desc')->first()->date;
           $last_sale_amount = sales::where('customerID', $customer)->orderBy('id', 'desc')->first()->net;
           $last_balance = getAccountBalanceOrderbookerWise($customer, $orderbooker);
        
           $methodData['last_sale'] = $last_sale;
           $methodData['last_sale_amount'] = round($last_sale_amount, 2);
           $methodData['last_balance'] = round($last_balance, 2);

        return view('orders.edit', compact('order', 'products', 'methodData'));
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
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $frieght = $request->fright[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim) + $frieght;
                $amount = $netPrice * $pc;
               
            
                $orderDetail = order_details::create([
                    'orderID' => $order->id,
                    'productID' => $id,
                    'customerID' => $order->customerID,
                    'orderbookerID' => $order->orderbookerID,
                    'branchID' => Auth()->user()->branchID,
                    'date' => $order->date,
                    'price'         => $price,
                    'discount'      => $discount,
                    'discountp'     => $request->discountp[$key],
                    'discountvalue' => $discountvalue,
                    'qty'           => $request->qty[$key],
                    'pc'            => $pc,
                    'loose'         => $request->loose[$key],
                    'netprice'      => $netPrice,
                    'amount'        => $amount,
                    'bonus'         => $request->bonus[$key],
                    'labor'         => $request->labor[$key],
                    'fright'        => $request->fright[$key],
                    'claim'         => $claim,
                    'unitID'        => $unit->id,
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
           $this->checkCompletion($order->id);
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Branch.orders')->with('success', 'Order updated');
    }

    public function finalize($orderID, $warehouseID)
    {
        $this->validateOrder($orderID);
        $order = orders::with('details.product', 'details.unit')->findOrFail($orderID);
        $customer = accounts::find($order->customerID);

        foreach($order->details as $pro)
        {
            $pro->stock = getWarehouseProductStock($pro->productID, $warehouseID);

        }
        $orderbooker = User::find($order->orderbookerID);
        $warehouse = warehouses::find($warehouseID);
        $supplymen = accounts::supplyMen()->get();
        
        return view('orders.finalize', compact('order', 'customer', 'orderbooker', 'warehouse', 'supplymen'));
    }

    public function storesale(request $request)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Nothing to Deliver');
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
                  'orderID'         => $request->orderID,
                  'transporter'     => $request->transporter,
                  'notes'           => $request->notes,
                  'edit'            => false,
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
                if($qty > 0)
                {
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
                        'price_amount'  => $price_amount,
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
                createStock($id, 0, $qty, $request->date, "Sold to $customer", $ref, $request->warehouseID);

                order_delivery::create(
                    [
                        'orderID'       => $request->orderID,
                        'salesID'       => $sale->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouseID,
                        'qty'           => $request->qty[$key],
                        'pc'            => $pc,
                        'loose'         => $request->loose[$key],
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
            }
            }

            $net = round($total,0);
            $totalLabor = round($totalLabor,0);

            $sale->update(
                [
                    'net' => $net,
                ]
            );

            $order = orders::find($request->orderID);
            $order->update([
                'status' => 'Under Process',
                'saleID' => $sale->id,
            ]);

            createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Sale No. $sale->id", $ref, $request->orderbookerID);
           
            createTransaction($request->supplymanID, $request->date, 0, $totalLabor, "Labor Charges of Sale No. $sale->id Customer: $customer", $ref, $request->orderbookerID);

            $this->checkCompletion($order->id);

            DB::commit();
            return to_route('Branch.orders')->with('success', "Sale Created");
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
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

        if($order->status == "Completed")
        {
            return redirect()->route('Branch.orders')->with('error', 'Order cannot be edited');
        }

        if($order->branchID != Auth()->user()->branchID)
        {
            return redirect()->route('Branch.orders')->with('error', 'Order does not belong to current branch');
        }

        return true;
    }

    public function checkCompletion($orderID)
    {
        $order = orders::findOrFail($orderID);
        if($order->status != "Completed")
        {
            $order_pc = $order->details->sum('pc');
            $delivered_pc = $order->delivered_items->sum('pc');

            if($order_pc == $delivered_pc)
            {
                $order->status = "Completed";
                $order->save();
            }
        }
        
    }
}
