<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class issue_advance extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employeeID');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
