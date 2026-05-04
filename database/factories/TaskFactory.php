<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Fix login bug', 'Update CSS styles', 'Implement API endpoint',
                'Refactor database schema', 'Write unit tests', 'Design UI mockup',
                'Optimize image loading', 'Deploy to staging', 'Review pull requests',
                'Fix responsive layout', 'Add user profile fields', 'Configure CI/CD',
                'Update documentation', 'Integrate payment gateway', 'Sanitize user input'
            ]),
            'description' => fake()->randomElement([
                'Fix the authentication bug preventing users from logging in with their credentials.',
                'Update the CSS styles to match the new design system and improve visual consistency.',
                'Implement the REST API endpoint for user management with proper validation.',
                'Refactor the database schema to improve query performance and reduce redundancy.',
                'Write comprehensive unit tests for the core business logic modules.',
                'Design UI mockups for the new dashboard feature based on user requirements.',
                'Optimize image loading to improve page load times and reduce bandwidth usage.',
                'Deploy the latest build to the staging environment for quality assurance testing.',
                'Review and merge pending pull requests to ensure code quality standards.',
                'Fix the responsive layout issues on mobile devices and tablets.',
                'Add new fields to the user profile section for better personalization options.',
                'Configure the CI/CD pipeline for automated testing and deployment.',
                'Update the technical documentation to reflect recent code changes.',
                'Integrate the payment gateway API for processing online transactions.',
                'Sanitize all user inputs to prevent SQL injection and XSS attacks.'
            ]),
            'status' => fake()->randomElement(['To Do', 'In Progress', 'Review', 'Completed']),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High']),
            'due_date' => fake()->dateTimeBetween('now', '+2 months'),
            'project_id' => null, // Should be provided by seeder
            'assigned_to' => null, // Should be provided by seeder
            'created_by' => null, // Should be provided by seeder
        ];
    }
}
