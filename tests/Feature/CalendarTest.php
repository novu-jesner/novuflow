<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_calendar_views_or_api()
    {
        $this->get(route('calendar.index'))->assertRedirect(route('login'));
        $this->getJson(route('calendar.tasks'))->assertStatus(401);
        $this->putJson(route('calendar.tasks.update-date', 1))->assertStatus(401);
    }

    public function test_authorized_user_can_access_calendar_index()
    {
        $user = User::factory()->create([
            'role' => 'Employee'
        ]);

        $response = $this->actingAs($user)->get(route('calendar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('calendar.index');
        $response->assertViewHasAll(['projects', 'users', 'teams']);
    }

    public function test_user_can_fetch_scoped_tasks()
    {
        $user = User::factory()->create(['role' => 'Employee']);
        $project = Project::factory()->create(['created_by' => $user->id]);
        
        // Sync project member status
        $project->members()->attach($user->id, ['status' => 'accepted']);

        // Create tasks
        $taskInScope = Task::factory()->create([
            'project_id' => $project->id,
            'due_date' => '2026-05-20',
            'created_by' => $user->id,
        ]);
        $taskInScope->assignees()->attach($user->id);

        $taskOutOfScope = Task::factory()->create([
            'due_date' => '2026-05-20',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('calendar.tasks', [
            'start' => '2026-05-01',
            'end' => '2026-05-31'
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'id' => $taskInScope->id,
            'title' => $taskInScope->title,
        ]);
    }

    public function test_user_can_fetch_scoped_tasks_by_team()
    {
        $user = User::factory()->create(['role' => 'Admin']);
        $team1 = \App\Models\Team::factory()->create(['leader_id' => $user->id]);
        $team2 = \App\Models\Team::factory()->create(['leader_id' => $user->id]);

        $project1 = Project::factory()->create(['team_id' => $team1->id, 'created_by' => $user->id]);
        $project2 = Project::factory()->create(['team_id' => $team2->id, 'created_by' => $user->id]);

        $task1 = Task::factory()->create([
            'project_id' => $project1->id,
            'due_date' => '2026-05-20',
            'created_by' => $user->id,
        ]);
        $task2 = Task::factory()->create([
            'project_id' => $project2->id,
            'due_date' => '2026-05-20',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('calendar.tasks', [
            'start' => '2026-05-01',
            'end' => '2026-05-31',
            'team_id' => $team1->id
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id' => $task1->id]);
        $response->assertJsonMissing(['id' => $task2->id]);
    }

    public function test_user_can_reschedule_assigned_task()
    {
        $user = User::factory()->create(['role' => 'Employee']);
        $project = Project::factory()->create(['created_by' => $user->id]);
        $project->members()->attach($user->id, ['status' => 'accepted']);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'due_date' => '2026-05-20',
            'created_by' => $user->id,
        ]);
        $task->assignees()->attach($user->id);

        $response = $this->actingAs($user)->putJson(route('calendar.tasks.update-date', $task->id), [
            'due_date' => '2026-05-25'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('2026-05-25', $task->fresh()->due_date->format('Y-m-d'));
    }

    public function test_user_cannot_reschedule_unassigned_task()
    {
        $user = User::factory()->create(['role' => 'Employee']);
        $otherUser = User::factory()->create(['role' => 'Employee']);
        $project = Project::factory()->create(['created_by' => $otherUser->id]);
        $project->members()->attach($otherUser->id, ['status' => 'accepted']);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'due_date' => '2026-05-20',
            'created_by' => $otherUser->id,
        ]);
        $task->assignees()->attach($otherUser->id);

        $response = $this->actingAs($user)->putJson(route('calendar.tasks.update-date', $task->id), [
            'due_date' => '2026-05-25'
        ]);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }
}
