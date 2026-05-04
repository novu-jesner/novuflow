<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = fake()->dateTimeBetween('now', '+6 months');

        return [
            'name' => fake()->randomElement([
                'Website Redesign', 'Mobile App V2', 'Cloud Migration', 
                'Internal Tooling', 'Security Audit', 'Customer Portal',
                'API Integration', 'Data Analytics Dashboard', 'Marketing Site'
            ]),
            'description' => fake()->randomElement([
                'A comprehensive project to redesign the website with modern UI/UX principles and improve user engagement.',
                'Building the second version of our mobile application with enhanced features and better performance.',
                'Migrating our infrastructure to cloud services for better scalability and cost efficiency.',
                'Developing internal tools to streamline our workflows and improve productivity.',
                'Conducting a thorough security audit to identify and fix potential vulnerabilities.',
                'Creating a customer-facing portal for self-service account management and support.',
                'Integrating third-party APIs to extend our product capabilities and provide better services.',
                'Building a data analytics dashboard to help stakeholders make informed decisions.',
                'Developing a new marketing website to showcase our products and attract new customers.'
            ]),
            'status' => fake()->randomElement(['Active', 'Completed', 'On Hold']),
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'progress' => fake()->numberBetween(0, 100),
            'team_id' => null, // Should be provided by seeder
            'created_by' => null, // Should be provided by seeder
        ];
    }
}
