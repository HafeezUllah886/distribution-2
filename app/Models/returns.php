<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class returns extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(returnsDetails::class, 'returnID');
    }

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function warehouse()
    {
        return $this->belongsTo(warehouses::class, 'warehouseID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', Auth()->user()->branchID);
    }
}
