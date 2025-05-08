<?php

namespace Database\Seeders;

use App\Models\product_dc;
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
            ['name' => "Test Product 1", 'nameurdu' => "ٹیسٹ 1", "vendorID" => 3,"pprice" => 100, "price" => 120, 'discount' => 5, 'discountp' => 2, 'fright' => 10, "labor" => 10, "claim" => 10, "sfright" => 14, "sclaim" => 14, 'catID' => 1, 'brandID' => 2],
            ['name' => "Test Product 2", 'nameurdu' => "ٹیسٹ 2", "vendorID" => 3,"pprice" => 20, "price" => 25, 'discount' => 2, 'discountp' => 3, 'fright' => 10, "labor" => 10, "claim" => 10, "sfright" => 14, "sclaim" => 14, 'catID' => 1, 'brandID' => 1],
            ['name' => "Test Product 3", 'nameurdu' => "ٹیسٹ 3", "vendorID" => 3,"pprice" => 850, "price" => 900, 'discount' => 15, 'discountp' => 1, 'fright' => 10, "labor" => 10, "claim" => 10, "sfright" => 14, "sclaim" => 14, 'catID' => 1, 'brandID' => 2],
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

        $data2 = [
            ['productID' => 1, 'areaID' => 1, 'dc' => 1],
            ['productID' => 1, 'areaID' => 2, 'dc' => 2],
            ['productID' => 1, 'areaID' => 3, 'dc' => 3],
            ['productID' => 1, 'areaID' => 4, 'dc' => 4],
            ['productID' => 2, 'areaID' => 1, 'dc' => 1],
            ['productID' => 2, 'areaID' => 2, 'dc' => 2],
            ['productID' => 2, 'areaID' => 3, 'dc' => 3],
            ['productID' => 2, 'areaID' => 4, 'dc' => 4],
            ['productID' => 3, 'areaID' => 1, 'dc' => 1],
            ['productID' => 3, 'areaID' => 2, 'dc' => 2],
            ['productID' => 3, 'areaID' => 3, 'dc' => 3],
            ['productID' => 3, 'areaID' => 4, 'dc' => 4],
        ];

        product_dc::insert($data2);
    }
}
