<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accounts extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeBusiness($query)
    {
        return $query->where('type', 'Business')->where('status', 'Active');
    }

    public function scopeCustomer($query)
    {
        return $query->where('type', 'Customer')->where('status', 'Active');
    }

    public function scopeVendor($query)
    {
        return $query->where('type', 'Vendor')->where('status', 'Active');

    }

    public function scopeOther($query)
    {
        return $query->whereNotIn('type', ['Business', 'Customer', 'Vendor']);

    }

    public function transactions()
    {
        return $this->hasMany(transactions::class, 'accountID');
    }

    public function sale()
    {
        return $this->hasMany(sales::class, 'customerID');
    }

    public function area()
{
    return $this->belongsTo(area::class, 'areaID');
}


}
