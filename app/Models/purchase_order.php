<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_order extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(accounts::class, 'vendorID');
    }

    public function details()
    {
        return $this->hasMany(purchase_order_details::class, 'orderID');
    }
    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function delivered_items()
    {
        return $this->hasMany(purchase_order_delivery::class, 'orderID');
    }

    public function totalQty()
    {
        $totalQty = $this->details()->sum('qty');
        $totalLooseQty = $this->details()->sum('loose');

        $total = "{$totalQty}, {$totalLooseQty}";
        return $total;
    }

    public function totalReceivedQty()
    {
        $totalQty = $this->delivered_items()->sum('qty');
        $totalLooseQty = $this->delivered_items()->sum('loose');

        $total = "{$totalQty}, {$totalLooseQty}";
        return $total;
    }

    public function totalPendingQty()
    {
        $totalQty = $this->details()->sum('qty') - $this->delivered_items()->sum('qty');
        $totalLooseQty = $this->details()->sum('loose') - $this->delivered_items()->sum('loose');

        $total = "{$totalQty}, {$totalLooseQty}";
        return $total;
    }
}
