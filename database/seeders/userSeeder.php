<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => "Admin",
            'password' => Hash::make("admin"),
            'role' => 'Admin',
            'contact' => "03451231237"
        ]);
        User::create([
            'name' => "Order Booker",
            'password' => Hash::make("orderbooker"),
            'role' => 'Order Booker',
            'contact' => "03451231238"
        ]);
    }
}
