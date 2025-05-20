<?php

namespace App\Http\Controllers;

use App\Models\order_delivery;
use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orders;
use App\Models\warehouses;
use Illuminate\Http\Request;

class OrderDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($orderID, $warehouseID)
    {
      $order = orders::findOrFail($orderID);
      $warehouse = warehouses::findOrFail($warehouseID);
      $supplymen = accounts::supplyMen()->currentBranch()->get();

      foreach ($order->details as $product) {
        $product->delivered = $product->delivered();
        $product->remaining = $product->remaining();
        $product->unit_value = $product->unit->value;
        $product->unit_name = $product->unit->unit_name;
        $product->stock = getWarehouseProductStock($product->productID, $warehouse->id);
      }

      return view('orders.delivery', compact('order', 'warehouse', 'supplymen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(order_delivery $order_delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(order_delivery $order_delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, order_delivery $order_delivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(order_delivery $order_delivery)
    {
        //
    }
}
