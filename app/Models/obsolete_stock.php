<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class obsolete_stock extends Model
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

    public function warehouse()
    {
        return $this->belongsTo(warehouses::class, 'warehouseID');
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', Auth()->user()->branchID);
    }
}
