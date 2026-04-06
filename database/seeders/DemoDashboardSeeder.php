<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;

return new class extends Seeder {
    public function run(): void {
        // Create demo users (skip if you already have users)
        User::factory()->count(3)->create(); // assumes you have UserFactory

        // Create demo projects
        Project::factory()->count(2)->create()->each(function($project) {
            // Create 5 tasks per project
            Task::factory()->count(5)->create([
                'project_id' => $project->id,
                'assigned_to' => User::inRandomOrder()->first()->id
            ]);
        });
    }
};
