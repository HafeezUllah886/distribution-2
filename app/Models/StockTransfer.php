<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function details(){
        return $this->hasMany(StockTransferDetails::class, 'stockTransferID');
    }

    public function fromWarehouse(){
        return $this->belongsTo(warehouses::class, 'from');
    }

    public function toWarehouse(){
        return $this->belongsTo(warehouses::class, 'to');
    }

    public function user(){
        return $this->belongsTo(User::class, 'createdBy');
    }

    
}
