<?php

use App\Models\delete_requests;
use App\Models\orderbooker_notifications;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\ref;
use App\Models\sale_details;
use App\Models\sales;
use App\Models\User;
use Carbon\Carbon;

function getRef()
{
    $ref = ref::first();
    if ($ref) {
        $ref->ref = $ref->ref + 1;
    } else {
        $ref = new ref;
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

function firstDayOfCurrentYear()
{
    $startOfYear = Carbon::now()->startOfYear();

    return $startOfYear->format('Y-m-d');
}

function lastDayOfCurrentYear()
{
    $endOfYear = Carbon::now()->endOfYear();

    return $endOfYear->format('Y-m-d');
}

function firstDayOfPreviousYear()
{
    $startOfPreviousYear = Carbon::now()->subYear()->startOfYear();

    return $startOfPreviousYear->format('Y-m-d');
}

function lastDayOfPreviousYear()
{
    $endOfPreviousYear = Carbon::now()->subYear()->endOfYear();

    return $endOfPreviousYear->format('Y-m-d');
}

function firstDayOfPreviousMonth()
{
    $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();

    return $startOfPreviousMonth->format('Y-m-d');
}

function lastDayOfPreviousMonth()
{
    $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();

    return $endOfPreviousMonth->format('Y-m-d');
}

function avgSalePrice($from, $to, $branch, $id)
{
    if ($branch == 'all') {
        $sales = sale_details::where('productID', $id);
    } else {
        $saleIDs = sales::where('branchID', $branch)->pluck('id')->toArray();
        $sales = sale_details::where('productID', $id)->whereIn('saleID', $saleIDs);
    }
    if ($from != 'all' && $to != 'all') {
        $sales->whereBetween('date', [$from, $to]);
    }
    $sales_amount = $sales->sum('amount');
    $sales_qty = $sales->sum('pc');

    if ($sales_qty > 0) {
        $sale_price = $sales_amount / $sales_qty;
    } else {
        $sale_price = 0;
    }

    return $sale_price;
}

function avgPurchasePrice($from, $to, $branch, $id)
{
    if ($branch == 'all') {
        $purchases = purchase_details::where('productID', $id);
    } else {
        $purchaseIDs = purchase::where('branchID', $branch)->pluck('id')->toArray();
        $purchases = purchase_details::where('productID', $id)->whereIn('purchaseID', $purchaseIDs);
    }
    if ($from != 'all' && $to != 'all') {
        $purchases->whereBetween('date', [$from, $to]);
    }
    $purchase_amount = $purchases->sum('amount');
    $purchase_qty = $purchases->sum('pc');

    if ($purchase_qty > 0) {
        $purchase_price = $purchase_amount / $purchase_qty;
    } else {
        $purchase_price = 0;
    }

    return $purchase_price;
}

function calculateGrowthPercentage($oldValue, $newValue)
{
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

    if ($purchase_qty > 0) {
        $purchase_price = $purchase_amount / $purchase_qty;
    } else {
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

    if ($sale_qty > 0) {
        $sale_price = $sale_amount / $sale_qty;
    } else {
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
    $purchase_fright = $purchases->avg('fright');

    $purchase_labor = $purchases->avg('labor');

    if ($purchase_qty > 0) {
        $purchase_price = ($purchase_amount / $purchase_qty) + $purchase_fright + $purchase_labor;
    } else {
        $purchase_price = 0;
    }

    return $purchase_price;
}

function avg_purchase_price_branch_wise($id, $branch)
{

    $purchase = purchase_details::where('productID', $id)
        ->whereHas('purchase', function ($q) use ($branch) {
            $q->where('branchID', $branch);
        })
        ->latest()
        ->take(10)
        ->get();

    $unit = products::find($id)->units->first() ? products::find($id)->units->first()->value : 1;

    if ($purchase->isEmpty()) {
        $product = products::find($id);

        $purchase_price = $product->price * $unit;
    } else {
        $purchase_price = $purchase->avg('price') * $unit;
    }

    return $purchase_price;
}

function avg_sale_price_branch_wise($id, $branch)
{

    $sale = sale_details::where('productID', $id)
        ->whereHas('sale', function ($q) use ($branch) {
            $q->where('branchID', $branch);
        })
        ->latest()
        ->take(20)
        ->get();
    $unit = products::find($id)->units->first() ? products::find($id)->units->first()->value : 1;

    if ($sale->isEmpty()) {
        $product = products::find($id);

        $sale_price = $product->price * $unit;
    } else {
        $sale_price = $sale->avg('price') * $unit;
    }

    return $sale_price;
}

function avg_cost_branch_wise($id, $branch)
{
    $purchases = purchase_details::where('productID', $id)
        ->whereHas('purchase', function ($q) use ($branch) {
            $q->where('branchID', $branch);
        });
    $purchases_data = $purchases->latest()->take(10)->get();
    $unit = products::find($id)->units->first() ? products::find($id)->units->first()->value : 1;

    if ($purchases_data->isEmpty()) {
        $last_purchase = purchase_details::where('productID', $id)
            ->whereHas('purchase', function ($q) use ($branch) {
                $q->where('branchID', $branch);
            });
        $last_purchase_data = $last_purchase->latest('date')->first();

        if ($last_purchase_data) {
            $purchase_price = $last_purchase_data->price;
            $purchase_discount = $last_purchase_data->discountvalue + $last_purchase_data->discount;
            $purchase_freight = $last_purchase_data->fright;
            $purchase_labor = $last_purchase_data->labor;
            $purchase_claim = $last_purchase_data->claim;
            $purchase_net = (($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim));
        } else {
            $product = products::find($id);
            $purchase_price = $product->pprice;
            $purchase_discount = 0;
            $purchase_freight = $product->fright;
            $purchase_labor = $product->labor;
            $purchase_claim = $product->claim;
            $purchase_net = (($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim));
        }
    } else {
        $purchase_price = $purchases_data->avg('price');
        $purchase_discount = $purchases_data->avg('discount');
        $purchase_discountP = $purchases_data->avg('discountvalue');
        $purchase_freight = $purchases_data->avg('fright');
        $purchase_labor = $purchases_data->avg('labor');
        $purchase_claim = $purchases_data->avg('claim');
        $purchase_discount = $purchase_discount + $purchase_discountP;

        $purchase_net = ((($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim)));
    }

    return $purchase_net;
}

function avg_cost_branch_wise_till_date($id, $branch, $date)
{
    $purchase = purchase::where('branchID', $branch)
        ->whereDate('date', '<=', $date)
        ->latest()
        ->take(10)
        ->pluck('id')->toArray();

    $purchases = purchase_details::where('productID', $id)
        ->whereIn('purchaseID', $purchase)
        ->get();

    $purchase_amount = $purchases->sum('amount');
    $purchase_qty = $purchases->sum('pc');

    if ($purchase_qty > 0) {
        $purchase_price = $purchase_amount / $purchase_qty;
    } else {
        $purchase_price = 0;
    }

    return $purchase_price;
}

function createNotification($orderbooker_id, $title, $message, $id, $model)
{
    $notification = new orderbooker_notifications;
    $notification->orderbooker_id = $orderbooker_id;
    $notification->title = $title;
    $notification->message = $message;
    $notification->ref_id = $id;
    $notification->model = $model;
    $notification->save();
}

function projectNameAuth()
{
    return 'GS Marketing';
}

function projectNameHeader()
{
    return 'GS MARKETING';
}
function projectNameShort()
{
    return 'GS';
}

function storeDeleteRequest($user_id, $branchID, $refID, $model, $notes)
{
    $check = delete_requests::where('refID', $refID)->first();
    if ($check && $check->status == 'pending') {
        return 0;
    }
    $notification = delete_requests::create([
        'user_id' => $user_id,
        'branchID' => $branchID,
        'refID' => $refID,
        'model' => $model,
        'notes' => $notes,
    ]);

    $notification_for = User::where('role', 'Branch Admin')->where('branchID', $branchID)->first();

    $user = User::where('id', $user_id)->first();
    \App\Models\Notification::create([
        'user_id' => $notification_for->id,
        'title' => 'Delete Request',
        'message' => 'Delete request received for '.$model.' from '.$user->name,
        'type' => 'info',
        'source' => 'delete_request',
        'reference_id' => $notification->id,
        'reference_type' => 'delete_request',
    ]);

    return 1;

}

function createUserNotification($user_id, $title, $message, $type = 'info', $source = null, $reference_id = null, $reference_type = null)
{
    \App\Models\Notification::create([
        'user_id' => $user_id,
        'title' => $title,
        'message' => $message,
        'type' => $type,
        'source' => $source,
        'reference_id' => $reference_id,
        'reference_type' => $reference_type,
    ]);
}
