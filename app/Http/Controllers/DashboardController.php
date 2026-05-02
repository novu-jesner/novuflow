<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $projectQuery = Project::with('team', 'members');
        $taskQuery = Task::with('project', 'assignee', 'creator', 'updater');
        $teamMemberQuery = User::whereHas('teams');

        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin') {
            // Get IDs of teams the user is involved in
            $teamIds = $user->teams->pluck('id')->toArray();
            if ($user->role === 'Team Leader') {
                $ledTeamIds = \App\Models\Team::where('leader_id', $user->id)->pluck('id')->toArray();
                $teamIds = array_unique(array_merge($teamIds, $ledTeamIds));
            }

            // Filter Projects
            $projectQuery->where(function($q) use ($user, $teamIds) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function($mq) use ($user) {
                      $mq->where('users.id', $user->id);
                  });
                
                if (!empty($teamIds)) {
                    $q->orWhereIn('team_id', $teamIds);
                }
            });

            // Filter Tasks based on project accessibility
            $taskQuery->whereHas('project', function($q) use ($user, $teamIds) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function($mq) use ($user) {
                      $mq->where('users.id', $user->id);
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
                // If user has no team, they only see themselves
                $teamMemberQuery->where('id', $user->id);
            }
        }

        $projects = $projectQuery->latest()->get();
        $tasks = $taskQuery->latest()->get();
        $teamMembers = $teamMemberQuery->latest()->get();

        return view('dashboard.index', compact('projects', 'tasks', 'teamMembers'));
    }

    public function myTasks()
    {
        $user = auth()->user();
        $tasks = Task::where('assigned_to', $user->id)
            ->with('project', 'assignee')
            ->latest()
            ->get();

        $todoTasks = $tasks->where('status', 'To Do');
        $inProgressTasks = $tasks->where('status', 'In Progress');
        $reviewTasks = $tasks->where('status', 'Review');
        $completedTasks = $tasks->where('status', 'Completed');

        return view('employee.tasks', compact('tasks', 'todoTasks', 'inProgressTasks', 'reviewTasks', 'completedTasks'));
    }

    public function adminUsers()
    {
        $users = User::latest()->get();
        $totalUsers = $users->count();
        $admins = $users->whereIn('role', ['SuperAdmin', 'Admin'])->count();
        $teamLeaders = $users->where('role', 'Team Leader')->count();
        $employees = $users->where('role', 'Employee')->count();

        return view('admin.users', compact('users', 'totalUsers', 'admins', 'teamLeaders', 'employees'));
    }

    public function adminAnalytics()
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

        return view('admin.analytics', compact('totalProjects', 'completedTasks', 'activeTasks', 'teamMembers', 'teams'));
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
        $user = User::findOrFail($id);
        return view('admin.users-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

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
        $user = User::findOrFail($id);
        
        // Prevent deleting self
        if (auth()->id() === $user->id) {
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
