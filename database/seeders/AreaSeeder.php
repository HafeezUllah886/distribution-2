<?php

namespace Database\Seeders;

use App\Models\area;
use App\Models\town;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => "Town 1"],
            ['name' => "Town 2"],
        ];
        town::insert($data);

        $data1 = [
            ['townID' => 1, 'name' => "Area 1"],
            ['townID' => 1, 'name' => "Area 2"],
            ['townID' => 2, 'name' => "Area 3"],
            ['townID' => 2, 'name' => "Area 4"],
        ];
        area::insert($data1);
    }
}
