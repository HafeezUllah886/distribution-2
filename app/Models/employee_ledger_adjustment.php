<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee_ledger_adjustment extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employeeID');
    }

    public function user()
    {
        return $this->belongsTo(user::class, 'userID');
    }
}
