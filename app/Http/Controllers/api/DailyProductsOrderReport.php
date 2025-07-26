<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\order_details;
use App\Models\orderbooker_products;
use App\Models\orders;
use App\Models\products;
use Illuminate\Http\Request;

class DailyProductsOrderReport extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from ?? date('Y-m-d');
        $to = $request->to ?? date('Y-m-d');

        $products = orderbooker_products::where('orderbookerID', $request->user()->id)->pluck('productID')->toArray();  
        
        $orders = orders::where('orderbookerID', $request->user()->id)->whereBetween('date', [$from, $to])->pluck('id')->toArray();

        $productData = [];
        foreach($products as $product)
        {
            $product = products::find($product);
            $unit_value = $product->units[0]->value;
            $pc = order_details::whereIn('orderID', $orders)->where('productID', $product->id)->sum('pc');

            $packQty = intdiv($pc, $unit_value);
            $looseQty = $pc % $unit_value;
    
            $totalAmount = order_details::whereIn('orderID', $orders)->where('productID', $product->id)->sum('amount');

            if($packQty > 0 || $looseQty > 0 || $totalAmount > 0)
            {
            $productData[$product->name] = [
                'totalQty'      => $packQty,
                'totalLooseQty' => $looseQty,
                'totalAmount'   => $totalAmount,
                'unit'          => $product->units[0]->unit_name,
                'packSize'      => $unit_value,
            ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'productData' => $productData,
            ]
        ], 200);
    }
}
