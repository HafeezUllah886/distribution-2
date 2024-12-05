<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_area extends Model
{

    use HasFactory;
    protected $guarded = [];

    public function area()
    {
        return $this->belongsTo(area::class, 'areaID');
    }

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }
}
