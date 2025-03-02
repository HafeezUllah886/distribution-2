<?php

use App\Models\material_stock;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\ref;
use App\Models\sale_details;
use App\Models\sales;
use App\Models\stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function getRef(){
    $ref = ref::first();
    if($ref){
        $ref->ref = $ref->ref + 1;
    }
    else{
        $ref = new ref();
        $ref->ref = 1;
    }
    $ref->save();
    dashboard();
    return $ref->ref;
}

function firstDayOfMonth()
{
    $startOfMonth = Carbon::now()->startOfMonth();

    return $startOfMonth->format('Y-m-d');
}
function lastDayOfMonth()
{

    $endOfMonth = Carbon::now()->endOfMonth();

    return $endOfMonth->format('Y-m-d');
}

function firstDayOfCurrentYear() {
    $startOfYear = Carbon::now()->startOfYear();
    return $startOfYear->format('Y-m-d');
}

function lastDayOfCurrentYear() {
    $endOfYear = Carbon::now()->endOfYear();
    return $endOfYear->format('Y-m-d');
}

function firstDayOfPreviousYear() {
    $startOfPreviousYear = Carbon::now()->subYear()->startOfYear();
    return $startOfPreviousYear->format('Y-m-d');
}

function lastDayOfPreviousYear() {
    $endOfPreviousYear = Carbon::now()->subYear()->endOfYear();
    return $endOfPreviousYear->format('Y-m-d');
}

function firstDayOfPreviousMonth() {
    $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
    return $startOfPreviousMonth->format('Y-m-d');
}

function lastDayOfPreviousMonth() {
    $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();
    return $endOfPreviousMonth->format('Y-m-d');
}

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


function avgSalePrice($from, $to, $branch, $id)
{
    if($branch == "all")
    {
    $sales = sale_details::where('productID', $id);
    }
    else
    {
        $saleIDs = sales::where('branchID', $branch)->pluck('id')->toArray();
        $sales = sale_details::where('productID', $id)->whereIn('saleID', $saleIDs);
    }
    if($from != 'all' && $to != 'all')
    {   
        $sales->whereBetween('date', [$from, $to]);
    }
    $sales_amount = $sales->sum('amount');
    $sales_qty = $sales->sum('qty');

    if($sales_qty > 0)
    {
        $sale_price = $sales_amount / $sales_qty;
    }
    else
    {
        $sale_price = 0;
    }

    return $sale_price;
}

function avgPurchasePrice($from, $to, $branch, $id)
{
    if($branch == "all")
    {
    $purchases = purchase_details::where('productID', $id);
    }
    else
    {
        $purchaseIDs = purchase::where('branchID', $branch)->pluck('id')->toArray();
        $purchases = purchase_details::where('productID', $id)->whereIn('purchaseID', $purchaseIDs);
    }
    if($from != 'all' && $to != 'all')
    {
        $purchases->whereBetween('date', [$from, $to]);
    }
    $purchase_amount = $purchases->sum('amount');
    $purchase_qty = $purchases->sum('qty');

    if($purchase_qty > 0)
    {
        $purchase_price = $purchase_amount / $purchase_qty;
    }
    else
    {
        $purchase_price = 0;
    }

    return $purchase_price;
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

function calculateGrowthPercentage($oldValue, $newValue) {
    if ($oldValue == 0) {
        return $newValue > 0 ? 100 : 0; // 100% growth if starting from 0 to any positive number
    }
    $growthPercentage = (($newValue - $oldValue) / $oldValue) * 100;
    return $growthPercentage;
}

function projectNameAuth()
{
    return "GS Marketing";
}

function projectNameHeader()
{
    return "GS MARKETING";
}
function projectNameShort()
{
    return "GS";
}
