<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class balance_targets extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function scopeCurrentBranch($query)
    {
        if (auth()->user()->role != 'Admin') {
            return $query->where('branchID', auth()->user()->branchID);
        }

        return $query;
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }
}
