<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customerAdvanceConsumption extends Model
{

    use HasFactory;

    protected $table = 'customer_advance_consumptions';

    protected $guarded = [];

    public function customerAdvance()
    {
        return $this->belongsTo(CustomerAdvancePayment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(sales::class, 'invoiceID', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(accounts::class, 'customerID', 'id');
    }

    public function orderbooker()
    {
        return $this->belongsTo(User::class, 'advance_orderbookerID', 'id');
    }

    public function consumptionOrderbooker()
    {
        return $this->belongsTo(User::class, 'consumption_orderbookerID', 'id');
    }
}
