<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expense_categories extends Model
{
    use HasFactory;

    protected $table = 'expense_categories';
    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany(expenses::class, 'categoryID');
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID', 'id');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

}
