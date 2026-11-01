<?php

namespace Database\Factories;

use App\Models\Election;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'election_id' => Election::factory(),
            'title' => fake()->randomElement([
                'President',
                'Vice President',
                'General Secretary',
                'Financial Secretary',
                'Director of Software & Projects',
                'Public Relations Officer',
            ]),
            'description' => fake()->sentence(10),
            'display_order' => fake()->numberBetween(1, 10),
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the position is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
