<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Teams for filtering
        $teamsQuery = \App\Models\Team::query();
        if (!$user->isAdmin()) {
            $teamsQuery->where(function ($q) use ($user) {
                $q->where('leader_id', $user->id)
                  ->orWhereHas('members', function ($mq) use ($user) {
                      $mq->where('users.id', $user->id);
                  });
            });
        }
        $teams = $teamsQuery->orderBy('name')->get();

        // Projects for filtering
        $projectsQuery = Project::query();
        if (!$user->isAdmin()) {
            $projectsQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function ($mq) use ($user) {
                      $mq->where('users.id', $user->id)
                         ->where('project_user.status', 'accepted');
                  });

                $teamIds = $user->teams->pluck('id')->toArray();
                if ($user->isTeamLeader()) {
                    $ledTeamIds = $user->ledTeams->pluck('id')->toArray();
                    $teamIds = array_unique(array_merge($teamIds, $ledTeamIds));
                }

                if (!empty($teamIds)) {
                    $q->orWhereIn('team_id', $teamIds);
                }
            });
        }
        $projects = $projectsQuery->orderBy('name')->get();

        // Users/assignees for filtering
        if ($user->isAdmin()) {
            $users = User::orderBy('name')->get();
        } else {
            $teamIds = $user->teams->pluck('id')->toArray();
            if ($user->isTeamLeader()) {
                $ledTeamIds = $user->ledTeams->pluck('id')->toArray();
                $teamIds = array_unique(array_merge($teamIds, $ledTeamIds));
            }
            $users = User::whereHas('teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })->orWhere('id', $user->id)->orderBy('name')->get();
        }

        // Map data to simple arrays for frontend JSON output to avoid complex inline Blade expression parsing
        $projectsJson = $projects->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'team_id' => $p->team_id
            ];
        });

        $usersJson = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'team_ids' => array_merge(
                    $u->teams->pluck('id')->toArray(),
                    \App\Models\Team::where('leader_id', $u->id)->pluck('id')->toArray()
                )
            ];
        });

        return view('calendar.index', compact('projects', 'users', 'teams', 'projectsJson', 'usersJson'));
    }

    public function getTasks(Request $request)
    {
        $user = Auth::user();
        $taskQuery = Task::with('project', 'assignees')
            ->whereNotNull('due_date');

        if ($request->filled('start') && $request->filled('end')) {
            $taskQuery->whereBetween('due_date', [$request->start, $request->end]);
        }

        if ($request->filled('team_id')) {
            $taskQuery->whereHas('project', function ($q) use ($request) {
                $q->where('team_id', $request->team_id);
            });
        }

        if ($request->filled('project_id')) {
            $taskQuery->where('project_id', $request->project_id);
        }

        if ($request->filled('assignee_id')) {
            $taskQuery->whereHas('assignees', function ($q) use ($request) {
                $q->where('users.id', $request->assignee_id);
            });
        }

        // Scope queries based on role
        if (!$user->isAdmin()) {
            $teamIds = $user->teams->pluck('id')->toArray();
            if ($user->isTeamLeader()) {
                $ledTeamIds = $user->ledTeams->pluck('id')->toArray();
                $teamIds = array_unique(array_merge($teamIds, $ledTeamIds));
            }

            $taskQuery->whereHas('project', function ($q) use ($user, $teamIds) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function ($mq) use ($user) {
                      $mq->where('users.id', $user->id)
                        ->where('project_user.status', 'accepted');
                  });

                if (!empty($teamIds)) {
                    $q->orWhereIn('team_id', $teamIds);
                }
            });
        }

        $tasks = $taskQuery->get()->map(function ($task) use ($user) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'due_date' => $task->due_date->format('Y-m-d'),
                'status' => $task->status,
                'priority' => $task->priority,
                'project_name' => $task->project ? $task->project->name : 'No Project',
                'project_id' => $task->project_id,
                'can_edit' => $user->isAdmin() ||
                              ($task->project && $task->project->created_by === $user->id) ||
                              ($task->project && $task->project->team && $user->isTeamLeader() && $task->project->team->leader_id === $user->id) ||
                              $task->assignees->contains('id', $user->id)
            ];
        });

        return response()->json($tasks);
    }

    public function updateDueDate(Request $request, $id)
    {
        $task = Task::with('project', 'assignees')->findOrFail($id);
        $user = Auth::user();

        // Check if user has permission to update this task
        $canEdit = $user->isAdmin() ||
                   ($task->project && $task->project->created_by === $user->id) ||
                   ($task->project && $task->project->team && $user->isTeamLeader() && $task->project->team->leader_id === $user->id) ||
                   $task->assignees->contains('id', $user->id);

        if (!$canEdit) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to reschedule this task.'], 403);
        }

        $validated = $request->validate([
            'due_date' => 'required|date',
        ]);

        $task->update([
            'due_date' => $validated['due_date'],
            'updated_by' => $user->id
        ]);

        return response()->json(['success' => true, 'message' => 'Task rescheduled successfully.']);
    }
}
