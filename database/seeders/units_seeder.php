<?php

namespace Database\Seeders;

use App\Models\units;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class units_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => "Piece", "value" => 1],
            ['name' => "Dzn", "value" => 12],
            ['name' => "Cotton", "value" => 6],
            ['name' => "Cotton", "value" => 12],
            ['name' => "Cotton", "value" => 24],
            ['name' => "Cotton", "value" => 24],
            ['name' => "Bag", "value" => 100],
        ];
        units::insert($data);
    }
}
