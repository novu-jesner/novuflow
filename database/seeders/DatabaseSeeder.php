<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users with different roles
        $admin = User::create([
            'name' => 'John Doe',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',
        ]);

        $teamLeader = User::create([
            'name' => 'Sarah Smith',
            'email' => 'leader@example.com',
            'password' => Hash::make('password'),
            'role' => 'Team Leader',
        ]);

        $employee1 = User::create([
            'name' => 'Mike Johnson',
            'email' => 'employee1@example.com',
            'password' => Hash::make('password'),
            'role' => 'Employee',
        ]);

        $employee2 = User::create([
            'name' => 'Emily Davis',
            'email' => 'employee2@example.com',
            'password' => Hash::make('password'),
            'role' => 'Employee',
        ]);

        // Create teams
        $devTeam = Team::create([
            'name' => 'Development Team',
            'description' => 'Frontend and backend developers',
            'leader_id' => $teamLeader->id,
        ]);

        $designTeam = Team::create([
            'name' => 'Design Team',
            'description' => 'UI/UX designers and creative team',
            'leader_id' => $teamLeader->id,
        ]);

        // Assign users to teams
        $devTeam->members()->attach([$admin->id, $teamLeader->id, $employee1->id]);
        $designTeam->members()->attach([$employee2->id]);

        // Create projects
        $project1 = Project::create([
            'name' => 'Website Redesign',
            'description' => 'Complete redesign of the company website',
            'status' => 'Active',
            'start_date' => '2024-01-01',
            'due_date' => '2024-06-30',
            'progress' => 65,
            'team_id' => $devTeam->id,
            'created_by' => $admin->id,
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Native mobile application for iOS and Android',
            'status' => 'Active',
            'start_date' => '2024-02-01',
            'due_date' => '2024-08-31',
            'progress' => 40,
            'team_id' => $devTeam->id,
            'created_by' => $admin->id,
        ]);

        $project3 = Project::create([
            'name' => 'Brand Identity',
            'description' => 'New brand identity and style guide',
            'status' => 'Active',
            'start_date' => '2024-01-15',
            'due_date' => '2024-04-30',
            'progress' => 80,
            'team_id' => $designTeam->id,
            'created_by' => $admin->id,
        ]);

        // Assign members to projects
        $project1->members()->attach([$admin->id, $teamLeader->id, $employee1->id]);
        $project2->members()->attach([$teamLeader->id, $employee1->id]);
        $project3->members()->attach([$employee2->id]);

        // Create tasks
        Task::create([
            'title' => 'Design homepage mockup',
            'description' => 'Create initial mockups for the homepage',
            'status' => 'Completed',
            'priority' => 'High',
            'due_date' => '2024-02-15',
            'project_id' => $project1->id,
            'assigned_to' => $employee1->id,
            'created_by' => $teamLeader->id,
        ]);

        Task::create([
            'title' => 'Implement authentication',
            'description' => 'Set up user authentication system',
            'status' => 'In Progress',
            'priority' => 'High',
            'due_date' => '2024-03-01',
            'project_id' => $project1->id,
            'assigned_to' => $teamLeader->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'title' => 'Database design',
            'description' => 'Design and implement database schema',
            'status' => 'To Do',
            'priority' => 'Medium',
            'due_date' => '2024-03-15',
            'project_id' => $project2->id,
            'assigned_to' => $employee1->id,
            'created_by' => $teamLeader->id,
        ]);

        Task::create([
            'title' => 'Create logo concepts',
            'description' => 'Design 5 different logo concepts',
            'status' => 'Review',
            'priority' => 'High',
            'due_date' => '2024-02-20',
            'project_id' => $project3->id,
            'assigned_to' => $employee2->id,
            'created_by' => $admin->id,
        ]);

        Task::create([
            'title' => 'API documentation',
            'description' => 'Document all API endpoints',
            'status' => 'To Do',
            'priority' => 'Low',
            'due_date' => '2024-04-01',
            'project_id' => $project2->id,
            'assigned_to' => $teamLeader->id,
            'created_by' => $admin->id,
        ]);
    }
}
