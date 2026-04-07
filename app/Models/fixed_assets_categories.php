<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fixed_assets_categories extends Model
{
    use HasFactory;

    protected $table = 'fixed_assets_categories';
    protected $guarded = [];

    public function fixed_assets()
    {
        return $this->hasMany(fixed_assets::class, 'categoryID');
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

}
