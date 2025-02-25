<?php

namespace Database\Seeders;

use App\Models\orderbooker_products;
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
            'contact' => "03451231237",
            'branchID' => 1,
        ]);
        $orderbooker = User::create([
            'name' => "Order Booker",
            'password' => Hash::make("orderbooker"),
            'role' => 'Order Booker',
            'contact' => "03451231238",
            'cashable' => 'no',
            'branchID' => 1,
        ]);

        orderbooker_products::create(
            [
                'orderbookerID' => $orderbooker->id,
                'productID' => 1,
            ]
        );
        orderbooker_products::create(
            [
                'orderbookerID' => $orderbooker->id,
                'productID' => 2,
            ]
        );

        User::create([
            'name' => "Operator",
            'password' => Hash::make("operator"),
            'role' => 'Operator',
            'contact' => "03451231238",
            'branchID' => 1,
        ]);

        User::create([
            'name' => "Branch Admin",
            'password' => Hash::make("admin"),
            'role' => 'Branch Admin',
            'contact' => "03451231238",
            'branchID' => 1,
        ]);

        User::create([
            'name' => "Accountant",
            'password' => Hash::make("accountant"),
            'role' => 'Accountant',
            'contact' => "03451231238",
            'branchID' => 1,
        ]);

    }
}
