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
        if(auth()->user()->role == 'Admin')
        {
            return $query->where('type', 'Business')->where('status', 'Active');
        }
        else
        {
            return $query->where('type', 'Business')->where('status', 'Active')->where('branchID', auth()->user()->branchID);
        }
       
    }

    public function scopeCustomer($query)
    {
        if(auth()->user()->role == 'Admin')
        {
            return $query->where('type', 'Customer')->where('status', 'Active');
        }
        else
        {
            return $query->where('type', 'Customer')->where('status', 'Active')->where('branchID', auth()->user()->branchID);
        }
       
    }

    public function scopeVendor($query)
    {
        if(auth()->user()->role == 'Admin')
        {
            return $query->where('type', 'Vendor')->where('status', 'Active');
        }
        else
        {
            return $query->where('type', 'Vendor')->where('status', 'Active')->where('branchID', auth()->user()->branchID);
        }
    }

    public function scopeLabor($query)
    {
        if(auth()->user()->role == 'Admin')
        {
            return $query->whereIn('type', ['Labor', 'Supply Man', 'Unloader'])->where('status', 'Active');
        }
        else
        {
            return $query->whereIn('type', ['Labor', 'Supply Man', 'Unloader'])->where('status', 'Active')->where('branchID', auth()->user()->branchID);
        }
    }

    public function scopeSupplyMen($query)
    {
        if(auth()->user()->role == 'Admin')
        {
            return $query->where('type', 'Supply Man')->where('status', 'Active');
        }
        else
        {
            return $query->where('type', 'Supply Man')->where('status', 'Active')->where('branchID', auth()->user()->branchID);
        }
    }

    public function scopeUnloader($query)
    {
        if(auth()->user()->role == 'Admin')
        {
            return $query->where('type', 'Unloader')->where('status', 'Active');
        }
        else
        {
            return $query->where('type', 'Unloader')->where('status', 'Active')->where('branchID', auth()->user()->branchID);
        }
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }

    public function scopeOther($query)
    {
        return $query->whereNotIn('type', ['Business', 'Customer', 'Vendor', 'Supply Order']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
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
    public function branch()
{
    return $this->belongsTo(branches::class, 'branchID');
}

public function vendor_products()
{
    return $this->hasMany(products::class, 'vendorID');
}


}
