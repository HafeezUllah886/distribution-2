<?php

namespace Database\Factories;

use App\Models\branches;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class employeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branchID' => branches::all()->random()->id,
            'name' => fake()->name(),
            'fname' => fake()->name(),
            'designation' => fake()->jobTitle(),
            'contact' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'salary' => fake()->numberBetween(10000, 50000),
            'limit' => fake()->numberBetween(100000, 500000),
            'doe' => fake()->date(),
            'status' => 'active',
        ];
    }
}
