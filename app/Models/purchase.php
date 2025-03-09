<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(accounts::class, 'vendorID');
    }

    public function details()
    {
        return $this->hasMany(purchase_details::class, 'purchaseID');
    }
    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function unloader()
    {
        return $this->belongsTo(accounts::class, 'unloaderID');
    }
}
