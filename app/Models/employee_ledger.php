<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee_ledger extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(employee::class, 'employeeID');
    }
    
}
