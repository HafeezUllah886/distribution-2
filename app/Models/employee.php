<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class employee extends Model
{

    use HasFactory;

    protected $guarded = [];
    protected $table = 'employees';

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentBranch($query)
    {
        if (Auth::check()) {
            return $query->where('branchID', Auth::user()->branchID);
        }
        return $query;
    }
}
