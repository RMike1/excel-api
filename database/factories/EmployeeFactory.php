<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'emp_no'=>fake()->numberBetween(300, 900),
            'first_name'=>fake()->firstName(),
            'last_name'=>fake()->lastName(),
            'hire_date'=>fake()->date(),
            'gender'=>fake()->title(),
            'birth_date'=>fake()->date(),
            'created_at'=>fake()->date(),
            'updated_at'=>fake()->date()
        ];
    }
}
