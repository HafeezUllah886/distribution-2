<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAdvancePayment extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID', 'id');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID', 'id');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
    
    public function consumption()
    {
        return $this->hasMany(customerAdvanceConsumption::class, 'customer_advanceID', 'id');
    }

    public function consumedAmount()
    {
        return $this->hasMany(customerAdvanceConsumption::class, 'customer_advanceID', 'id')->sum('amount');
    }

    public function remainingAmount()
    {
        return $this->amount - $this->consumedAmount();
    }
}
