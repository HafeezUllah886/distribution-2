<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\orderbooker_products;
use Illuminate\Http\Request;

class nonFinanancialInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function orderbooker_products(Request $request)
    {
        $orderbooker_products = $request->user()->products()->with('product')->get();

        $products = [];
        foreach ($orderbooker_products as $product) {
            if($product->product->status == "In-active")
            {
                continue;
            }
            $products[] = [
                'id' => $product->product->id,
                'name' => $product->product->name,
                'name_urdu' => $product->product->nameurdu,
                'price' => $product->product->price,
                'units' => $product->product->units()->select('id', 'unit_name', 'value')->get(),
            ];
        }
        return [
            'products' => $products,
        ];
    }

    public function customers(Request $request)
    {
        $orderbooker_customers = orderbooker_customers::where('orderbookerID', $request->user()->id)->get();

        $customers = accounts::customer()->whereIn('id', $orderbooker_customers->pluck('customerID'))->select('id', 'branchID', 'title', 'address', 'contact', 'email', 'c_type', 'credit_limit', 'areaID', 'status')->get();

        foreach($customers as $customer)
        {
            $customer->curren_balance = getAccountBalance($customer->id);
            $customer->area = $customer->area->name;
        }

        return [
            'customers' => $customers,
        ];
    }
}
