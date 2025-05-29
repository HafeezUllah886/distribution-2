<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paymentsReceiving extends Model
{

    use HasFactory;

    protected $guarded = [];
    protected $table = 'payments_receiving';

    public function depositer()
    {
        return $this->belongsTo(accounts::class, 'depositerID');
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
