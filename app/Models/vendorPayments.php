<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vendorPayments extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(accounts::class, 'vendorID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
