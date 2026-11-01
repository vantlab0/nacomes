<?php

namespace Database\Factories;

use App\Models\Election;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Election>
 */
class ElectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'NACOMES Departmental Executive Elections',
            'description' => 'Annual general elections for the executive council of the Department of Computer Engineering.',
            'status' => 'open',
            'results_published' => false,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ];
    }

    /**
     * Indicate that the election is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the election is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Indicate that the election results are authorized for public publication.
     */
    public function resultsPublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'results_published' => true,
        ]);
    }
}
