<?php

namespace Database\Seeders;

use App\Models\employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\employeeFactory;

class employeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        employee::factory(10)->create();
    }
}
