<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact',
        'branchID',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeOrderbookers($query)
    {
        if(Auth()->user()->role == "Admin")
        {
            return $query->where('role', 'Order Booker');
        }
        else
        {
            return $query->where('role', 'Order Booker')->where('branchID', Auth()->user()->branchID);
        }
    }

    public function scopeAccountants($query)
    {
        if(Auth()->user()->role == "Admin")
        {
            return $query->where('role', 'Accountant');
        }
        else
        {
            return $query->where('role', 'Accountant')->where('branchID', Auth()->user()->branchID);
        }
       
    }
    public function scopeOperators($query)
    {
        if(Auth()->user()->role == "Admin")
        {
            return $query->where('role', 'Operator');
        }
        else
        {
            return $query->where('role', 'Operator')->where('branchID', Auth()->user()->branchID);
        }
    }

    public function products()
    {
        return $this->hasMany(orderbooker_products::class, 'orderbookerID');
    }

    public function branch()
    {
        return $this->belongsTo(branches::class, "branchID");
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branchID', auth()->user()->branchID);
    }
}
