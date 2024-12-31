<?php

namespace Database\Seeders;

use App\Models\warehouses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class warehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => "Main Warehouse", 'branchID' => 1, 'address' => "Main Road, Lahore", 'contact' => "03451231237"],
            ['name' => "Warehouse I", 'branchID' => 1, 'address' => "Branch Road, Lahore", 'contact' => "03451231238"],
        ];
        warehouses::insert($data);
    }
}
