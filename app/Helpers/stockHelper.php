<?php

use App\Models\products;
use App\Models\stock;
use App\Models\warehouses;
use Illuminate\Support\Facades\DB;

function createStock($id, $cr, $db, $date, $notes, $ref, $warehouse)
{
    stock::create(
        [
            'productID'     => $id,
            'cr'            => $cr,
            'db'            => $db,
            'date'          => $date,
            'notes'         => $notes,
            'refID'         => $ref,
            'warehouseID'   => $warehouse
        ]
    );
}

function getStock($id){
    if(Auth()->user()->role == "admin"){
        $stocks  = stock::where('productID', $id)->get();
    }
    else{
        $warehouses = DB::table('warehouses')->where('branchID', auth()->user()->branchID)->distinct()->pluck('id')->toArray();
        $stocks  = stock::where('productID', $id)->whereIn('warehouseID', $warehouses)->get();
    }

    $balance = 0;
    foreach($stocks as $stock)
    {
    $balance += $stock->cr;
    $balance -= $stock->db;
    }
    return $balance;
}

function getBranchProductStock($id, $branch){
    
        $warehouses = DB::table('warehouses')->where('branchID', $branch)->distinct()->pluck('id')->toArray();
        $stocks  = stock::where('productID', $id)->whereIn('warehouseID', $warehouses)->get();
    
    $balance = 0;
    foreach($stocks as $stock)
    {
    $balance += $stock->cr;
    $balance -= $stock->db;
    }
    return $balance;
}

function getWarehouseProductStock($id, $warehouse){
    $stocks  = stock::where('productID', $id)->where('warehouseID', $warehouse)->get();
    $balance = 0;
    foreach($stocks as $stock)
    {
        $balance += $stock->cr;
        $balance -= $stock->db;
    }

    return $balance;
}


function stockValue()
{
    $products = products::all();

    $value = 0;
    foreach($products as $product)
    {
        $value += productStockValue($product->id);
    }

    return $value;
}

function productStockValue($id)
{
    $stock = getStock($id);
    $price = avgPurchasePrice('all', 'all','all', $id);
    dashboard();
    return $price * $stock;
}

function productStockValues($id)
{
    $stock = getStock($id);
    $price = avgSalePrice('all', 'all', 'all', $id);
    dashboard();
    return $price * $stock;
}

function warehouse_product_stock_value_purchase_wise($id, $warehouse)
{
    $stock = getWarehouseProductStock($id, $warehouse);
    $price = avg_purchase_price_warehouse_wise($id, $warehouse);
    
    return $price * $stock;
}
function warehouse_product_stock_value_sale_wise($id, $warehouse)
{
    $stock = getWarehouseProductStock($id, $warehouse);
    $price = avg_sale_price_warehouse_wise($id, $warehouse);
    
    return $price * $stock;
}
function warehouse_product_stock_value_cost_wise($id, $warehouse)
{
    $stock = getWarehouseProductStock($id, $warehouse);
    $price = avg_cost_warehouse_wise($id, $warehouse);
    
    return $price * $stock;
}

function branch_product_stock_value_purchase_wise($id, $branch)
{
    $stock = getBranchProductStock($id, $branch);
    $price = avg_purchase_price_branch_wise($id, $branch);
    
    return $price * $stock;
}
function branch_product_stock_value_sale_wise($id, $branch)
{
    $stock = getBranchProductStock($id, $branch);
    $price = avg_sale_price_branch_wise($id, $branch);
    
    return $price * $stock;
}
function branch_product_stock_value_cost_wise($id, $branch)
{
    $stock = getBranchProductStock($id, $branch);
    $price = avg_cost_branch_wise($id, $branch);
    
    return $price * $stock;
}

function packInfo($size, $name, $qty)
 {
    $packs = intdiv($qty, $size);
    $remains = $qty - ($packs*$size);
    if($packs == 0 && $remains == 0)
    {
        return "0 Pcs";
    }
    if($packs == 0)
    {
        return "$remains Pcs";
    }
    if($remains == 0)
    {
        return "$packs $name";
    }
    return "$packs $name, $remains Pcs";
 }

 function packInfoWithOutName($size, $qty)
{
    $packs = intdiv($qty, $size);
    $remains = $qty - ($packs*$size);
    return $packs . "," . $remains;
 }
