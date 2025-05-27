<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class method_transactions extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}
