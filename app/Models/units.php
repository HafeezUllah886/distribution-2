<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class units extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID', 'id');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
