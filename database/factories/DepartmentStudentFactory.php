<?php

namespace Database\Factories;

use App\Models\DepartmentStudent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DepartmentStudent>
 */
class DepartmentStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'matric_no' => 'CET/CPE/'.fake()->unique()->numerify('####').'/2025',
            'department' => DepartmentStudent::DEPARTMENT,
            'level' => 100,
        ];
    }
}
