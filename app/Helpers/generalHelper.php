<?php

use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\ref;
use App\Models\sale_details;
use App\Models\sales;
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
    $sales_qty = $sales->sum('pc');

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
    $purchase_qty = $purchases->sum('pc');

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

function calculateGrowthPercentage($oldValue, $newValue) {
    if ($oldValue == 0) {
        return $newValue > 0 ? 100 : 0; // 100% growth if starting from 0 to any positive number
    }
    $growthPercentage = (($newValue - $oldValue) / $oldValue) * 100;
    return $growthPercentage;
}

function avg_purchase_price_warehouse_wise($id, $warehouse)
{
    $purchases = purchase_details::where('productID', $id)
        ->where('warehouseID', $warehouse)
        ->latest()
        ->take(10)
        ->get();

    $purchase_amount = $purchases->sum('price_amount');
    $purchase_qty = $purchases->sum('pc');

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
function avg_sale_price_warehouse_wise($id, $warehouse)
{
    $sales = sale_details::where('productID', $id)
        ->where('warehouseID', $warehouse)
        ->latest()
        ->take(20)
        ->get();

    $sale_amount = $sales->sum('price_amount');
    $sale_qty = $sales->sum('pc');

    if($sale_qty > 0)
    {
        $sale_price = $sale_amount / $sale_qty;
    }
    else
    {
        $sale_price = 0;
    }
    return $sale_price;
}

function avg_cost_warehouse_wise($id, $warehouse)
{
    $purchases = purchase_details::where('productID', $id)
        ->where('warehouseID', $warehouse)
        ->latest()
        ->take(10)
        ->get();

    $purchase_amount = $purchases->sum('amount');
    $purchase_qty = $purchases->sum('pc');

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

function avg_purchase_price_branch_wise($id, $branch)
{

    $purchase = purchase::where('branchID', $branch)
        ->latest()
        ->take(10)
        ->pluck('id')->toArray();

    $purchases = purchase_details::where('productID', $id)
        ->whereIn('purchaseID', $purchase)
        ->get();

    $purchase_amount = $purchases->sum('price_amount');
    $purchase_qty = $purchases->sum('pc');

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

function avg_sale_price_branch_wise($id, $branch)
{

    $sale = sales::where('branchID', $branch)
        ->latest()
        ->take(20)
        ->pluck('id')->toArray();

    $sales = sale_details::where('productID', $id)
        ->whereIn('saleID', $sale)
        ->get();

    $sale_amount = $sales->sum('price_amount');
    $sale_qty = $sales->sum('pc');

    if($sale_qty > 0)
    {
        $sale_price = $sale_amount / $sale_qty;
    }
    else
    {
        $sale_price = 0;
    }
    return $sale_price;
}

function avg_cost_branch_wise($id, $branch)
{
    $purchase = purchase::where('branchID', $branch)
        ->latest()
        ->take(10)
        ->pluck('id')->toArray();

    $purchases = purchase_details::where('productID', $id)
        ->whereIn('purchaseID', $purchase)
        ->get();

    $purchase_amount = $purchases->sum('amount');
    $purchase_qty = $purchases->sum('pc');

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


