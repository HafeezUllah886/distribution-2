<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\stock;
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
            if ($product->product->status == 'In-active') {
                continue;
            }
            $discountValue = $product->product->price * $product->product->discountp / 100;
            $products[] = [
                'id' => $product->product->id,
                'name' => $product->product->name,
                'name_urdu' => $product->product->nameurdu,
                'vendor_name' => $product->product->vendor->title,
                'vendor_name_urdu' => $product->product->vendor->title_urdu,
                'image' => asset($product->product->image_path ?? 'images/products/no-img.jpg'),
                'price' => ($product->product->price - $product->product->discount - $discountValue - $product->product->sclaim) + $product->product->sfright,
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

        $customers = accounts::customer()->whereIn('id', $orderbooker_customers->pluck('customerID'))->select('id', 'branchID', 'title', 'title_urdu', 'address', 'address_urdu', 'contact', 'email', 'c_type', 'credit_limit', 'areaID', 'status')->get();

        foreach ($customers as $customer) {
            $customer->curren_balance = getAccountBalanceOrderbookerWise($customer->id, $request->user()->id);
            $customer->area = $customer->area->name;
        }

        return [
            'customers' => $customers,
        ];
    }

    public function orderbooker_products_stock_report(Request $request)
    {
        $orderbooker_products = $request->user()->products()->with('product')->get();

        $products = [];
        foreach ($orderbooker_products as $product) {
            if ($product->product->status == 'In-active') {
                continue;
            }

            $cr = stock::where('productID', $product->product->id)->sum('cr');
            $db = stock::where('productID', $product->product->id)->sum('db');
            $stock = $cr - $db;

            $unit = $product->product->units()->first();
            $stock_info = packInfoWithOutName($unit->value, $stock);
            $pack_qty = explode(',', $stock_info);

            $products[] = [
                'id' => $product->product->id,
                'name' => $product->product->name,
                'name_urdu' => $product->product->nameurdu,
                'price' => $product->product->price,
                'vendor_name' => $product->product->vendor->title,
                'image' => asset($product->product->image_path ?? 'images/products/no-img.jpg'),
                'unit_name' => $unit->unit_name,
                'pack_size' => $unit->value,
                'pack_qty' => (int) $pack_qty[0],
                'loose_qty' => (int) $pack_qty[1],
                'total_stock' => $stock,
            ];
        }

        return [
            'products' => $products,
        ];
    }
}
