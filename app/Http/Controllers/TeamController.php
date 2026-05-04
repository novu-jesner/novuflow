<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index($id = null)
    {
        $user = auth()->user();
        $team = null;
        
        if ($id) {
            $team = Team::with('leader', 'members', 'projects')->findOrFail($id);
            $members = $team->members;
        } elseif ($user->role === 'Team Leader') {
            $team = Team::where('leader_id', $user->id)->first();
            $members = $team ? $team->members : collect();
        } elseif ($user->isAdmin()) {
            $members = User::with('teams')->latest()->get();
            // For admins without a specific team context, maybe just show everyone or no stats
        } else {
            $team = $user->teams()->first();
            $members = $team ? $team->members : collect();
        }

        // Calculate Stats
        if ($team) {
            $projectIds = $team->projects->pluck('id');
            $activeTasks = Task::whereIn('project_id', $projectIds)->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
            $completedTasks = Task::whereIn('project_id', $projectIds)->where('status', 'Completed')->count();
        } else {
            // Fallback for Admins or users without teams
            $activeTasks = Task::whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
            $completedTasks = Task::where('status', 'Completed')->count();
        }
        
        $totalMembers = $members->count();
        $avgCompletion = ($activeTasks + $completedTasks) > 0 
            ? round(($completedTasks / ($activeTasks + $completedTasks)) * 100) 
            : 0;

        $availableUsers = $team ? User::whereNotIn('id', $members->pluck('id'))->get() : collect();

        $teams = Team::with('leader', 'members')->latest()->get();
        
        return view('team.index', compact('teams', 'members', 'totalMembers', 'activeTasks', 'completedTasks', 'avgCompletion', 'availableUsers', 'team'));
    }

    public function adminTeams()
    {
        $teams = Team::with('leader', 'members', 'projects')->latest()->get();
        $totalTeams = $teams->count();
        $totalMembers = User::count();
        $activeProjects = 0;
        foreach ($teams as $team) {
            $activeProjects += $team->projects()->where('status', 'Active')->count();
        }
        $avgTeamSize = $totalTeams > 0 ? round($totalMembers / $totalTeams) : 0;

        return view('admin.teams', compact('teams', 'totalTeams', 'totalMembers', 'activeProjects', 'avgTeamSize'));
    }

    public function create()
    {
        $leaders = User::where('role', 'Team Leader')->whereDoesntHave('ledTeams')->get();
        $allUsers = User::all();
        return view('admin.teams-create', compact('leaders', 'allUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'leader_id' => $validated['leader_id'],
        ]);

        if (!empty($validated['member_ids'])) {
            $team->members()->attach($validated['member_ids']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Team created successfully!', 'redirect' => route('admin.teams')]);
        }

        return redirect()->route('admin.teams')->with('success', 'Team created successfully!');
    }

    public function edit($id)
    {
        $team = Team::with('leader', 'members')->findOrFail($id);
        $leaders = User::where('role', 'Team Leader')
            ->where(function($query) use ($team) {
                $query->whereDoesntHave('ledTeams')
                      ->orWhere('id', $team->leader_id);
            })
            ->get();
        $allUsers = User::all();
        return view('admin.teams-edit', compact('team', 'leaders', 'allUsers'));
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'leader_id' => $validated['leader_id'],
        ]);

        if (isset($validated['member_ids'])) {
            $team->members()->sync($validated['member_ids']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Team updated successfully!',
                'redirect' => route('admin.teams')
            ]);
        }

        return redirect()->route('admin.teams')->with('success', 'Team updated successfully!');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->members()->detach();
        $team->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Team deleted successfully!']);
        }

        return redirect()->route('admin.teams')->with('success', 'Team deleted successfully!');
    }

    public function inviteMember(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = User::where('email', $validated['email'])->first();
        $team = Team::findOrFail($validated['team_id']);

        $team->members()->attach($user->id);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Member invited successfully!']);
        }

        return back()->with('success', 'Member invited successfully!');
    }

    public function memberProfile($id)
    {
        $user = User::with('teams', 'tasks')->findOrFail($id);
        return view('team.member-profile', compact('user'));
    }

    public function assignTasks(Request $request, $id)
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        $user = User::findOrFail($id);
        foreach ($validated['task_ids'] as $taskId) {
            $task = Task::findOrFail($taskId);
            $task->update(['assigned_to' => $user->id]);
        }

        return back()->with('success', 'Tasks assigned successfully!');
    }

    public function changeRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:Employee,Team Leader,Admin,SuperAdmin',
        ]);

        $user = User::findOrFail($id);
        $user->update(['role' => $validated['role']]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Role changed successfully!']);
        }

        return back()->with('success', 'Role changed successfully!');
    }

    public function removeMember($id)
    {
        $user = User::findOrFail($id);
        $team = auth()->user()->teams()->first();
        
        if ($team) {
            $team->members()->detach($user->id);
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Member removed successfully!']);
        }

        return back()->with('success', 'Member removed successfully!');
    }
}
