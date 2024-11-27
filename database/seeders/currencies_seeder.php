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
            ['title' => "5000"],
            ['title' => "1000"],
            ['title' => "100"],
            ['title' => "75"],
            ['title' => "50"],
            ['title' => "20"],
            ['title' => "10"],
            ['title' => "5"],
            ['title' => "2"],
            ['title' => "1"],
            ['title' => "Others"],
        ];
        currencymgmt::insert($data);
    }
}
