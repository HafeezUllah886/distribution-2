<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_delivery extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(orders::class, 'orderID');
        
    }

    public function product()
    {
        return $this->belongsTo(products::class, 'productID');
    }

    public function warehouse()
    {
        return $this->belongsTo(warehouses::class, 'warehouseID');
    }

    public function unit()
    {
        return $this->belongsTo(product_units::class, 'unitID');
    }

   
}
