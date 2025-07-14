<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class issue_misc extends Model
{

    use HasFactory;

    protected $guarded = [];
    protected $table = 'issue_miscs';

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employeeID');
    }

    public function cat()
    {
        return $this->belongsTo(employees_payment_cats::class, 'catID');
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function ScopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
