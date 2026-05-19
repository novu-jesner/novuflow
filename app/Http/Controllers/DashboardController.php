<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $projectQuery = Project::with('team', 'members');
        $taskQuery = Task::with('project', 'assignees', 'creator', 'updater');
        $teamMemberQuery = User::whereHas('teams');

        // Identify accessible teams
        $teamIds = $user->teams->pluck('id')->toArray();
        if ($user->isTeamLeader()) {
            $ledTeamIds = $user->ledTeams->pluck('id')->toArray();
            $teamIds = array_unique(array_merge($teamIds, $ledTeamIds));
        }

        if (!$user->isAdmin()) {
            // Filter Projects: created by user, OR member with accepted status, OR in user's teams
            $projectQuery->where(function($q) use ($user, $teamIds) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function($mq) use ($user) {
                      $mq->where('users.id', $user->id)
                        ->where('project_user.status', 'accepted');
                  });
                
                if (!empty($teamIds)) {
                    $q->orWhereIn('team_id', $teamIds);
                }
            });

            // Filter Tasks based on project accessibility
            $taskQuery->whereHas('project', function($q) use ($user, $teamIds) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function($mq) use ($user) {
                      $mq->where('users.id', $user->id)
                        ->where('project_user.status', 'accepted');
                  });
                
                if (!empty($teamIds)) {
                    $q->orWhereIn('team_id', $teamIds);
                }
            });

            // Filter Team Members to only those in the user's teams
            if (!empty($teamIds)) {
                $teamMemberQuery->whereHas('teams', function($q) use ($teamIds) {
                    $q->whereIn('teams.id', $teamIds);
                });
            } else {
                $teamMemberQuery->where('id', $user->id);
            }
        }

        $projects = $projectQuery->latest()->get();
        $tasks = $taskQuery->latest()->get();
        
        // Finalize Team Members with scoped stats
        $teamMembers = $teamMemberQuery->latest()->get()->map(function($member) use ($projects) {
            $projectIds = $projects->pluck('id')->toArray();
            
            $member->dashboard_completed_tasks = Task::whereHas('assignees', function($q) use ($member) { $q->where('users.id', $member->id); })->where('status', 'Completed')->count();
            $member->dashboard_active_tasks = Task::whereHas('assignees', function($q) use ($member) { $q->where('users.id', $member->id); })->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
                
            return $member;
        });

        // Pre-calculate stats for the view
        $teamOnlineCount = $teamMembers->where('is_online', true)->count();
        $teamOfflineCount = $teamMembers->where('is_online', false)->count();
        
        $stats = [
            'total_projects' => $projects->count(),
            'active_tasks' => $tasks->whereIn('status', ['To Do', 'In Progress', 'Review'])->count(),
            'completed_tasks' => $tasks->where('status', 'Completed')->count(),
            'team_members_count' => $teamMembers->count(),
            'team_online' => $teamOnlineCount,
            'team_offline' => $teamOfflineCount,
        ];

        return view('dashboard.index', compact('projects', 'tasks', 'teamMembers', 'stats'));
    }

    public function myTasks(Request $request)
    {
        $user = Auth::user();
        $tasksQuery = Task::with(['project.team', 'assignees', 'creator', 'updater']);

        if ($user->isEmployee()) {
            $tasksQuery->whereHas('assignees', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        } elseif ($user->isTeamLeader()) {
            $ledTeamIds = $user->ledTeams->pluck('id')->toArray();
            $tasksQuery->whereHas('project.team', function ($q) use ($ledTeamIds) {
                $q->whereIn('teams.id', $ledTeamIds);
            });
        }
        // For admin, no additional where clause - show all tasks

        if ($request->filled('status') && $request->status !== 'all') {
            $tasksQuery->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $tasksQuery->where('priority', $request->priority);
        }

        if ($request->filled('assignee') && $request->assignee !== 'all') {
            if ($request->assignee === 'unassigned') {
                $tasksQuery->whereNull('assigned_to');
            } else {
                $tasksQuery->whereHas('assignees', function ($q) use ($request) {
                    $q->where('users.id', $request->assignee);
                });
            }
        }

        if ($request->filled('project_id') && $request->project_id !== 'all') {
            $tasksQuery->where('project_id', $request->project_id);
        }

        if ($request->filled('team_id') && $request->team_id !== 'all' && $user->isAdmin()) {
            $tasksQuery->whereHas('project.team', function ($q) use ($request) {
                $q->where('teams.id', $request->team_id);
            });
        }

        if ($request->filled('due_date_start') || $request->filled('due_date_end')) {
            if ($request->filled('due_date_start') && $request->filled('due_date_end')) {
                $start = Carbon::parse($request->due_date_start)->startOfDay();
                $end = Carbon::parse($request->due_date_end)->endOfDay();
                $tasksQuery->whereBetween('due_date', [$start, $end]);
            } elseif ($request->filled('due_date_start')) {
                $tasksQuery->where('due_date', '>=', Carbon::parse($request->due_date_start)->startOfDay());
            } else {
                $tasksQuery->where('due_date', '<=', Carbon::parse($request->due_date_end)->endOfDay());
            }
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $tasksQuery->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('project', function ($pq) use ($searchTerm) {
                      $pq->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('project.team', function ($tq) use ($searchTerm) {
                      $tq->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('assignees', function ($aq) use ($searchTerm) {
                      $aq->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('creator', function ($cq) use ($searchTerm) {
                      $cq->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'title':
                $tasksQuery->orderBy('title', $sortDirection);
                break;
            case 'priority':
                $tasksQuery->orderByRaw("CASE priority WHEN 'High' THEN 1 WHEN 'Medium' THEN 2 WHEN 'Low' THEN 3 END " . $sortDirection);
                break;
            case 'due_date':
                $tasksQuery->orderBy('due_date', $sortDirection);
                break;
            case 'assignee':
                $tasksQuery->leftJoin('task_user', 'tasks.id', '=', 'task_user.task_id')
                          ->leftJoin('users', 'task_user.user_id', '=', 'users.id')
                          ->select('tasks.*', DB::raw('MIN(users.name) as first_assignee_name'))
                          ->groupBy('tasks.id')
                          ->orderBy('first_assignee_name', $sortDirection);
                break;
            case 'project':
                $tasksQuery->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                          ->orderBy('projects.name', $sortDirection)
                          ->select('tasks.*');
                break;
            case 'team':
                $tasksQuery->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
                          ->leftJoin('teams', 'projects.team_id', '=', 'teams.id')
                          ->orderBy('teams.name', $sortDirection)
                          ->select('tasks.*');
                break;
            default:
                $tasksQuery->orderBy('created_at', $sortDirection);
        }

        $tasks = $tasksQuery->get();

        $projectIds = $tasks->pluck('project_id')->unique();
        $columnOrders = \App\Models\ProjectColumn::whereIn('project_id', $projectIds)
            ->get()
            ->groupBy('name')
            ->map(fn($cols) => $cols->min('order'));

        $statuses = $tasks->pluck('status')->unique()->values()->sortBy(function ($status) use ($columnOrders) {
            return $columnOrders->get($status, 999);
        })->values();

        $groupedTasks = $tasks->groupBy('status');

        // Limit data based on user role
        if ($user->isEmployee()) {
            $projects = Project::whereIn('id', $projectIds)->orderBy('name')->get();
            $teams = Team::whereHas('projects', function($q) use ($projectIds) {
                $q->whereIn('projects.id', $projectIds);
            })->orderBy('name')->get();
            $assignees = User::whereHas('assignedTasks', function($q) use ($tasks) {
                $q->whereIn('tasks.id', $tasks->pluck('id'));
            })->orderBy('name')->get();
        } elseif ($user->isTeamLeader()) {
            $ledTeamIds = $user->ledTeams->pluck('id')->toArray();
            $projects = Project::whereIn('team_id', $ledTeamIds)->orderBy('name')->get();
            $teams = Team::whereIn('id', $ledTeamIds)->orderBy('name')->get();
            $assignees = User::whereHas('assignedTasks', function($q) use ($tasks) {
                $q->whereIn('tasks.id', $tasks->pluck('id'));
            })->orderBy('name')->get();
        } else {
            // Admin
            $projects = Project::orderBy('name')->get();
            $teams = Team::orderBy('name')->get();
            $assignees = User::orderBy('name')->get();
        }

        $stats = [
            'total' => $tasks->count(),
            'overdue' => $tasks->where('due_date', '<', now()->toDateString())->count(),
            'due_today' => $tasks->where('due_date', now()->toDateString())->count(),
            'high_priority' => $tasks->where('priority', 'High')->count(),
            'completed' => $tasks->where('status', 'Completed')->count(),
            'unassigned' => $tasks->whereNull('assigned_to')->count(),
        ];

        return view('employee.tasks', compact('tasks', 'statuses', 'groupedTasks', 'projects', 'teams', 'assignees', 'stats'));
    }

    public function adminUsers()
    {
        $authUser = Auth::user();
        $usersQuery = User::latest();

        // If current user is Admin (not SuperAdmin), hide SuperAdmins
        if ($authUser->role === 'Admin') {
            $usersQuery->where('role', '!=', 'SuperAdmin');
        }

        $users = $usersQuery->get();
        $totalUsers = $users->count();
        $admins = $users->whereIn('role', ['SuperAdmin', 'Admin'])->count();
        $teamLeaders = $users->where('role', 'Team Leader')->count();
        $employees = $users->where('role', 'Employee')->count();

        return view('admin.users', compact('users', 'totalUsers', 'admins', 'teamLeaders', 'employees'));
    }

    public function adminAnalytics(Request $request)
    {
        $totalProjects = Project::count();
        $completedTasks = Task::where('status', 'Completed')->count();
        $activeTasks = Task::whereIn('status', ['To Do', 'In Progress'])->count();
        $teamMembers = User::count();
        
        $teams = \App\Models\Team::with(['leader', 'projects.tasks'])
            ->withCount('members')
            ->latest()
            ->get()
            ->map(function($team) {
                $allTasks = $team->projects->flatMap->tasks;
                $totalTasks = $allTasks->count();
                $completed = $allTasks->where('status', 'Completed')->count();
                
                $team->projects_count = $team->projects->count();
                $team->completion_rate = $totalTasks > 0 ? round(($completed / $totalTasks) * 100) : 0;
                $team->overdue_tasks = $allTasks->where('status', '!=', 'Completed')
                    ->where('due_date', '<', now())
                    ->whereNotNull('due_date')
                    ->count();
                
                return $team;
            });

        $projects = Project::orderBy('name')->get();
        $selectedProjectId = $request->input('project_id', $projects->first()?->id);
        $selectedProject = $projects->firstWhere('id', $selectedProjectId);

        $heatmap = [];
        $timeline = [];

        if ($selectedProject) {
            $histories = TaskStatusHistory::whereHas('task', function ($query) use ($selectedProject) {
                    $query->where('project_id', $selectedProject->id);
                })
                ->with('changedBy')
                ->orderBy('created_at')
                ->get();

            $durations = $histories
                ->groupBy('new_status')
                ->map(fn ($group) => $group->sum(fn ($history) => $history->duration_in_seconds ?? 0))
                ->toArray();

            $columns = ['To Do', 'In Progress', 'Ready for Review', 'Completed'];
            $heatmap = collect($columns)
                ->map(fn ($column) => [
                    'column' => $column,
                    'duration_seconds' => $durations[$column] ?? 0,
                    'duration_label' => $this->formatDuration($durations[$column] ?? 0),
                    'intensity' => $this->heatmapIntensity($durations[$column] ?? 0),
                    'color' => $this->heatmapColor($durations[$column] ?? 0),
                ])
                ->toArray();

            $timeline = $histories
                ->map(fn ($history) => [
                    'task_id' => $history->task_id,
                    'old_status' => $history->old_status,
                    'new_status' => $history->new_status,
                    'changed_at' => $history->created_at->format('Y-m-d H:i:s'),
                    'changed_by' => $history->changedBy?->name,
                ])
                ->groupBy(fn ($entry) => substr($entry['changed_at'], 0, 13) . ':00')
                ->map(fn ($entries, $bucket) => [
                    'bucket' => $bucket,
                    'events' => $entries,
                    'counts' => collect($entries)->countBy('new_status')->toArray(),
                ])
                ->values()
                ->toArray();
        }

        return view('admin.analytics', compact('totalProjects', 'completedTasks', 'activeTasks', 'teamMembers', 'teams', 'projects', 'selectedProjectId', 'heatmap', 'timeline'));
    }

    protected function heatmapIntensity(int $seconds): string
    {
        if ($seconds >= 43200) {
            return 'critical';
        }

        if ($seconds >= 14400) {
            return 'warning';
        }

        return 'safe';
    }

    protected function heatmapColor(int $seconds): string
    {
        return match ($this->heatmapIntensity($seconds)) {
            'critical' => 'bg-red-500/20 ring-red-500/40 text-red-700',
            'warning' => 'bg-orange-400/20 ring-orange-400/35 text-orange-700',
            default => 'bg-emerald-400/15 ring-emerald-500/20 text-emerald-700',
        };
    }

    protected function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds === 1 ? '1 second' : "{$seconds} seconds";
        }

        if ($seconds < 3600) {
            $minutes = (int) round($seconds / 60);
            return $minutes === 1 ? '1 minute' : "{$minutes} minutes";
        }

        if ($seconds < 86400) {
            $hours = (int) round($seconds / 3600);
            return $hours === 1 ? '1 hour' : "{$hours} hours";
        }

        if ($seconds < 604800) {
            $days = (int) round($seconds / 86400);
            return $days === 1 ? '1 day' : "{$days} days";
        }

        if ($seconds < 2629746) {
            $weeks = (int) round($seconds / 604800);
            return $weeks === 1 ? '1 week' : "{$weeks} weeks";
        }

        if ($seconds < 31556952) {
            $months = (int) round($seconds / 2629746);
            return $months === 1 ? '1 month' : "{$months} months";
        }

        $years = (int) round($seconds / 31556952);
        return $years === 1 ? '1 year' : "{$years} years";
    }

    public function createUser()
    {
        return view('admin.users-create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Employee,Team Leader,Admin,SuperAdmin',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User created successfully!', 'redirect' => route('admin.users')]);
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    public function editUser($id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        // Prevent Admin from editing SuperAdmin
        if ($authUser->role === 'Admin' && $user->role === 'SuperAdmin') {
            abort(403, 'You do not have permission to edit SuperAdmin users.');
        }

        return view('admin.users-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        // Prevent Admin from updating SuperAdmin
        if ($authUser->role === 'Admin' && $user->role === 'SuperAdmin') {
            abort(403, 'You do not have permission to update SuperAdmin users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:Employee,Team Leader,Admin,SuperAdmin',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'User updated successfully!',
                'redirect' => route('admin.users')
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function destroyUser($id)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        // Prevent Admin from deleting SuperAdmin
        if ($authUser->role === 'Admin' && $user->role === 'SuperAdmin') {
            abort(403, 'You do not have permission to delete SuperAdmin users.');
        }
        
        // Prevent deleting self
        if (Auth::id() === $user->id) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
            }
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Check if user has tasks or projects
        if ($user->assignedTasks()->exists() || $user->createdTasks()->exists() || $user->createdProjects()->exists()) {
            $message = 'User cannot be deleted because they have associated tasks or projects. Please reassign or delete them first.';
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }
}
