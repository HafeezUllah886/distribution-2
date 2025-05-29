<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bulk_payments extends Model
{

    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
