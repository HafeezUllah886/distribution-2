<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fixed_assets_sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function fixed_asset()
    {
        return $this->belongsTo(fixed_assets::class, 'fixedAssetID');
    }

}
