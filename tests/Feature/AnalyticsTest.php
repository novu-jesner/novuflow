<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_analytics()
    {
        $response = $this->get(route('admin.analytics'));
        $response->assertRedirect(route('login'));
    }

    public function test_employee_cannot_access_admin_analytics()
    {
        $employee = User::factory()->create([
            'role' => 'Employee'
        ]);

        $response = $this->actingAs($employee)->get(route('admin.analytics'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_analytics()
    {
        $admin = User::factory()->create([
            'role' => 'Admin'
        ]);

        $project = Project::factory()->create(['created_by' => $admin->id]);
        
        // Let's create some columns
        $project->columns()->createMany([
            ['name' => 'To Do', 'order' => 0],
            ['name' => 'In Progress', 'order' => 1],
            ['name' => 'Completed', 'order' => 2],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics');
        $response->assertViewHasAll([
            'totalProjects',
            'completedTasks',
            'activeTasks',
            'teamMembers',
            'teams',
            'projects',
            'selectedProjectId',
            'heatmap',
            'timeline',
            'playbackTasks',
            'selectedProjectColumns'
        ]);
    }
}
