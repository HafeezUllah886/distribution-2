<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID');
    }

    public function details()
    {
        return $this->hasMany(sale_details::class, 'saleID');
    }

    public function payments()
    {
        return $this->hasMany(sale_payments::class, 'salesID');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'orderbookerID');
    }

    public function supplyman()
    {
        return $this->belongsTo(accounts::class, 'supplymanID');
    }

    public function scopePaid()
    {
        return $this->payments()->sum('amount');
    }

    public function scopeDue()
    {
        return $this->net - $this->scopePaid();
    }

    public function scopePaidStatus($query)
    {
        return $query->whereRaw('(net - (SELECT COALESCE(SUM(amount), 0) FROM sale_payments WHERE salesID = sales.id)) <= 0');
    }

    public function scopeDueStatus($query)
    {
        return $query->whereRaw('(net - (SELECT COALESCE(SUM(amount), 0) FROM sale_payments WHERE salesID = sales.id)) > 0');
    }

    public function scopeUnpaidOrPartiallyPaid($query)
    {
        return $query->whereRaw('net > (SELECT COALESCE(SUM(amount), 0) FROM sale_payments WHERE salesID = sales.id)');
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, 'branchID');
    }

    public function age()
{
    return (int) Carbon::parse($this->date)->diffInDays(now());
}


}
