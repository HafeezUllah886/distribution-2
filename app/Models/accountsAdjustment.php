<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accountsAdjustment extends Model
{

    use HasFactory;
    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(accounts::class, 'accountID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function orderbooker()
    {
        $transaction = transactions::where('refID', $this->refID)->first();
        return $transaction->orderbooker->name;
    }
}
