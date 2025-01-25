<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_payments extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function bill()
    {
        return $this->belongsTo(sales::class, 'salesID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}
