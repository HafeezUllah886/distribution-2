<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class staffPayments extends Model
{

    use HasFactory;
    protected $guarded = [];

    public function staff()
    {
        return $this->belongsTo(User::class, 'fromID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'receivedBy');
    }
}
