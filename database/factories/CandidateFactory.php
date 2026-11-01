<?php

namespace Database\Factories;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidate>
 */
class CandidateFactory extends Factory
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
            'position_id' => fn (array $attributes) => Position::factory()->create([
                'election_id' => $attributes['election_id'],
            ])->id,
            'name' => fake()->name(),
            'matric_no' => 'CPE/'.fake()->numberBetween(2019, 2023).'/'.fake()->unique()->numerify('####'),
            'academic_level' => fake()->randomElement(['300 Level', '400 Level', '500 Level']),
            'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the candidate is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
