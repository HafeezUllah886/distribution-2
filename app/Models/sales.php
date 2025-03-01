<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function details()
    {
        return $this->hasMany(sale_details::class, 'saleID');
    }

    public function payments()
    {
        return $this->hasMany(sale_payments::class, 'salesID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function supplyman()
    {
        return $this->belongsTo(accounts::class, 'supplymanID');
    }

    public function scopePaid()
    {
        return $this->payments()->sum('amount');
    }

    public function scopeDue()
    {
        return $this->net - $this->scopePaid();
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

}
