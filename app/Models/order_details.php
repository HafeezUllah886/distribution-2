<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_details extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(products::class, 'productID');
    }

    public function unit()
    {
        return $this->belongsTo(product_units::class, 'unitID');
    }

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function order()
    {
        return $this->belongsTo(orders::class, 'orderID');
    }

    public function scopeDelivered()
    {
        return order_delivery::where('orderID', $this->orderID)->where('productID', $this->productID)->sum('pc');
    }

    public function scopeRemaining()
    {
        return $this->pc - $this->scopeDelivered();
    }

    public function scopeDeliveredAmount()
    {
        $pcs =$this->pc;
        $amount = $this->amount;

        $unit_price = $amount / $pcs;

        return order_delivery::where('orderID', $this->orderID)->where('productID', $this->productID)->sum('pc') * $unit_price;
    }

    public function lastDelivery()
    {
        return order_delivery::where('orderID', $this->orderID)->where('productID', $this->productID)->orderBy('id', 'desc')->first()->created_at ?? null;
    }

}
