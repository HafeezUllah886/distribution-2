<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class labor_payments extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function labor()
    {
        return $this->belongsTo(accounts::class, 'laborID');
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
