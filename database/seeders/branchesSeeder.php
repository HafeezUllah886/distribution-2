<?php

namespace Database\Seeders;

use App\Models\branches;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class branchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => "Main Branch", 'address' => "Main Street", 'contact' => "1234567890"],
            ['name' => "Branch 1", 'address' => "Branch 1 Street", 'contact' => "1234567890"],

        ];
        branches::insert($data);
    }
}
