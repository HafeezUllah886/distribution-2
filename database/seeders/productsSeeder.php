<?php

namespace Database\Seeders;

use App\Models\product_units;
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
            ['name' => "Test Product 1", 'nameurdu' => "ٹیسٹ 1", "vendorID" => 3,"pprice" => '1200', "price" => 1640, 'discount' => 30, 'fright' => 10, "labor" => 10, "claim" => 10, 'catID' => 1, 'brandID' => 2],
            ['name' => "Test Product 2", 'nameurdu' => "ٹیسٹ 2", "vendorID" => 3,"pprice" => '1200', "price" => 1640, 'discount' => 30, 'fright' => 10, "labor" => 10, "claim" => 10, 'catID' => 1, 'brandID' => 1],
            ['name' => "Test Product 3", 'nameurdu' => "ٹیسٹ 3", "vendorID" => 3,"pprice" => '850', "price" => 1050, 'discount' => 150, 'fright' => 10, "labor" => 10, "claim" => 10, 'catID' => 1, 'brandID' => 2],
        ];
        products::insert($data);

        $data1 = [
            ['productID' => 1, 'unit_name' => 'Cotton', 'value' => 24],
            ['productID' => 1, 'unit_name' => 'Dzn', 'value' => 12],
            ['productID' => 2, 'unit_name' => 'Pieces', 'value' => 1],
            ['productID' => 2, 'unit_name' => 'Box', 'value' => 10],
            ['productID' => 3, 'unit_name' => 'Pack', 'value' => 50],
            ['productID' => 3, 'unit_name' => 'Bag', 'value' => 100],
        ];

        product_units::insert($data1);
    }
}
