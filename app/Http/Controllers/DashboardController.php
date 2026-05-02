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
        $projects = Project::with('team')->latest()->take(5)->get();
        $tasks = Task::with('project', 'assignee', 'creator')->latest()->take(5)->get();
        $teamMembers = User::whereHas('teams')->latest()->take(5)->get();

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

        return view('admin.analytics', compact('totalProjects', 'completedTasks', 'activeTasks', 'teamMembers'));
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
            return response()->json(['success' => true, 'message' => 'User updated successfully!']);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
        }

        return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
    }
}
