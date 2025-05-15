<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\sales;
use App\Models\sale_details;
use App\Models\product_units;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'customerID' => 'required|exists:customers,id',
                'warehouseID' => 'required|exists:warehouses,id',
                'orderbookerID' => 'required|exists:users,id',
                'supplymanID' => 'required|exists:users,id',
                'orderdate' => 'required|date',
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
