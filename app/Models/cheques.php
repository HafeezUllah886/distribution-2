<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cheques extends Model
{

    use HasFactory;

    protected $table = 'cheques';
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function forwarded_account()
    {
        return $this->belongsTo(accounts::class, 'forwardedTo', "id");
    }
}
