<?php

namespace Database\Seeders;

use App\Models\currencymgmt;
use App\Models\units;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class currencies_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['title' => "5000", 'value' => '5000'],
            ['title' => "1000", 'value' => '1000'],
            ['title' => "500", 'value' => '500'],
            ['title' => "100", 'value' => '100'],
            ['title' => "75", 'value' => '75'],
            ['title' => "50", 'value' => '50'],
            ['title' => "20", 'value' => '20'],
            ['title' => "10", 'value' => '10'],
            ['title' => "5", 'value' => '5'],
            ['title' => "2", 'value' => '2'],
            ['title' => "1", 'value' => '1'],
        ];
        currencymgmt::insert($data);
    }
}
