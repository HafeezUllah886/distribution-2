<?php

namespace Database\Seeders;

use App\Models\products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class productsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code' => "3423423454", 'name' => "Test Product 1", "unitID" => 1, "pprice" => 1540 , "wsprice" => 1620, "price" => 1640, 'tp' => 1525.42, 'discount' => 30, 'catID' => 1, 'brandID' => 2],
            ['code' => "5645645655", 'name' => "Test Product 2", "unitID" => 1, "pprice" => 1540 , "wsprice" => 1620, "price" => 1640, 'tp' => 1525.42, 'discount' => 30, 'catID' => 1, 'brandID' => 1],
            ['code' => "8656443423", 'name' => "Test Product 3", "unitID" => 1, "pprice" => 900 , "wsprice" => 1000, "price" => 1050, 'tp' => 955.93, 'discount' => 150, 'catID' => 1, 'brandID' => 2],
        ];
        products::insert($data);
    }
}
