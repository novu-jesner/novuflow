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
        // 1. Create 2 admins
        $admin1 = User::create([
            'name' => 'John Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',
        ]);

        $admin2 = User::create([
            'name' => 'Jane Admin',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        $teamLeader1 = User::create([
            'name' => 'Sarah Leader',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'role' => 'Team Leader',
        ]);

        $teamLeader2 = User::create([
            'name' => 'Mike Leader',
            'email' => 'mike@example.com',
            'password' => Hash::make('password'),
            'role' => 'Team Leader',
        ]);

        // 2. Create additional employees - each will be in only one team
        $employees = [];
        for ($i = 1; $i <= 8; $i++) {
            $employees[] = User::factory()->create(['role' => 'Employee']);
        }
        $allEmployees = collect($employees);
        $allUsers = collect([$admin1, $admin2, $teamLeader1, $teamLeader2])->concat($allEmployees);

        // 3. Create 2 teams with specific leaders
        $team1 = Team::create([
            'name' => 'Engineering Team',
            'description' => 'Software development team',
            'leader_id' => $teamLeader1->id,
        ]);

        $team2 = Team::create([
            'name' => 'Design Team',
            'description' => 'UI/UX design team',
            'leader_id' => $teamLeader2->id,
        ]);

        // 4. Assign employees to teams - each employee in only ONE team
        // First 4 employees go to team1, remaining 4 go to team2
        $team1Members = $allEmployees->slice(0, 4);
        $team2Members = $allEmployees->slice(4, 4);

        // Sync members to teams (team leader + 4 employees = 5 members each)
        $team1->members()->sync($team1Members->pluck('id')->push($teamLeader1->id)->toArray());
        $team2->members()->sync($team2Members->pluck('id')->push($teamLeader2->id)->toArray());

        $teams = collect([$team1, $team2]);

        // 5. Create projects for each team (only team members, no external users)
        foreach ($teams as $team) {
            $team->load('members');
            $teamMembers = $team->members;
            
            $project = Project::factory()->create([
                'team_id' => $team->id,
                'created_by' => $team->leader_id,
            ]);

            foreach ($teamMembers as $member) {
                $project->members()->attach($member->id, ['status' => 'accepted']);
            }

            // Create default columns
            $columns = ['To Do', 'In Progress', 'Review', 'Completed'];
            foreach ($columns as $index => $name) {
                \DB::table('project_columns')->insert([
                    'project_id' => $project->id,
                    'name' => $name,
                    'order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create tasks - only assign to employees
            $assignees = $teamMembers->filter(fn($user) => $user->role === 'Employee');
            
            for ($j = 0; $j < 10; $j++) {
                $assignee = $assignees->random();
                Task::factory()->create([
                    'project_id' => $project->id,
                    'assigned_to' => $assignee->id,
                    'created_by' => $team->leader_id,
                ]);
            }
        }

    }
}
