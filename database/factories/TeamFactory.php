<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Engineering', 'Design', 'Marketing', 'Product', 'Operations', 'Support']) . ' Team',
            'description' => 'A dedicated team focusing on ' . fake()->randomElement(['software development', 'user experience design', 'digital marketing', 'product strategy', 'operational excellence', 'customer success']) . ' and delivering high-quality results.',
            'leader_id' => null, // Should be provided by seeder
        ];
    }
}
