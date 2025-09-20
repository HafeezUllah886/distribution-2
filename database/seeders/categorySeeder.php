<?php

namespace Database\Seeders;

use App\Models\categories;
use App\Models\expense_categories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class categorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cats = [
            ['name' => 'Category 1', 'branchID' => 1],
            ['name' => 'Category 2', 'branchID' => 1],
        ];

        categories::insert($cats);


        $expenseCats = [
            ['name' => 'Expense Category 1', 'branchID' => 1],
            ['name' => 'Expense Category 2', 'branchID' => 1],
        ];

        expense_categories::insert($expenseCats);
    }
}
