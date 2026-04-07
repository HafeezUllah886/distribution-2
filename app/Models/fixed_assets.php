<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fixed_assets extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function category()
    {
        return $this->belongsTo(fixed_assets_categories::class, 'categoryID');
    }

    public function sale()
    {
        return $this->hasOne(fixed_assets_sales::class, 'fixedAssetID');
    }

    public function status()
    {
        return $this->sale()->count() > 0 ? "Sold" : "Available";
    }
}
