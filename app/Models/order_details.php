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

}
