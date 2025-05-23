<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit_withdraw extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function account()
    {
        return $this->belongsTo(accounts::class, 'accountID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
