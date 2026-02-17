<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notification_settings extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'week_days' => 'array',
    ];
}
