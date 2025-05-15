<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class branches extends Model
{

    use HasFactory;

    protected $guarded = [];
    
    /**
     * Get the header attribute with a default value if null
     *
     * @return string
     */
    public function getHeaderAttribute($value)
    {
        return $value ?? 'assets/header/default.jpg';
    }

}
