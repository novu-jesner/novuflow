<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Project;

class TaskFactory extends Factory
{
    protected $model = \App\Models\Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['todo', 'doing', 'done']),
            'project_id' => Project::inRandomOrder()->first()->id ?? 1,
            'assigned_to' => User::inRandomOrder()->first()->id ?? null,
        ];
    }
}