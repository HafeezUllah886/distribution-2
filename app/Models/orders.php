<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function details()
    {
        return $this->hasMany(order_details::class, 'orderID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }
}
