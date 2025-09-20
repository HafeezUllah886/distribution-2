<?php

namespace Database\Seeders;

use App\Models\brands;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class brandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Brand 1', 'branchID' => 1],
            ['name' => 'Brand 2', 'branchID' => 1],
        ];

        brands::insert($brands);
    }
}
