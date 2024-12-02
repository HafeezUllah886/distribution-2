<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class town extends Model
{

    use HasFactory;

    protected $guarded = [];

    public function areas()
    {
        return $this->hasMany(area::class, 'townID');
    }
}
