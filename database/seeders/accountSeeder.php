<?php

namespace Database\Seeders;

use App\Models\accounts;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class accountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        accounts::create(
            [
                'title' => "Business",
                'type' => "Business",
                'category' => "Bank",
                'areaID' => 1,
                'branchID' => 1,
            ]
        );

        accounts::create(
            [
                'title' => "Walk-In Customer",
                'type' => "Customer",
                'areaID' => 1,
                'branchID' => 1,

            ]
        );

        accounts::create(
            [
                'title' => "Walk-In Vendor",
                'type' => "Vendor",
                'areaID' => 1,
                'branchID' => 1,
            ]
        );

        accounts::create(
            [
                'title' => "Supply Man",
                'type' => "Supply Man",
                'areaID' => 1,
                'branchID' => 1,
            ]
        );
    }
}
