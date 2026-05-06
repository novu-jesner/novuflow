<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectColumn;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Project::with('team', 'creator', 'members');

        if ($user->role === 'Employee') {
            // Employees only see projects they are accepted members of
            $query->whereHas('members', function($q) use ($user) {
                $q->where('users.id', $user->id)
                  ->where('project_user.status', 'accepted');
            });
        } elseif ($user->role === 'Team Leader') {
            // Team Leaders see projects they lead OR are members of
            $query->where(function($q) use ($user) {
                $teamIds = \App\Models\Team::where('leader_id', $user->id)->pluck('id');
                $q->whereIn('team_id', $teamIds)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('users.id', $user->id)
                        ->where('project_user.status', 'accepted');
                  });
            });
        }
        // SuperAdmin and Admin see all projects (no filtering)

        $projects = $query->latest()->get();
        
        $userTeam = null;
        $teamMembers = collect();
        
        if ($user->role === 'Team Leader') {
            $userTeam = \App\Models\Team::where('leader_id', $user->id)->first();
            if ($userTeam) {
                $teamMembers = $userTeam->members;
            }
        }

        return view('projects.index', compact('projects', 'userTeam', 'teamMembers'));
    }


public function create()
{
    $authUser = auth()->user();

    // ❌ Block Employee
    if ($authUser->isEmployee()) {
            return redirect()->route('projects.index')
    ->with('error', 'You are not allowed to create projects.');
    }

    // ✅ Admin → all teams
    if ($authUser->isAdmin()) {
        $teams = Team::with('members')->get();
    } 
    // ✅ Team Leader → only their team
    elseif ($authUser->isTeamLeader()) {
        $teams = Team::where('leader_id', $authUser->id)
            ->with('members')
            ->get();

        // 🚨 IMPORTANT: handle no team case
        if ($teams->isEmpty()) {
            return redirect()->back()
                ->with('error', 'You do not have a team assigned.');
        }
    }

    return view('projects.create', compact('teams'));
}

public function store(Request $request)
{
    $authUser = auth()->user();

    // ❌ Block Employees
    if ($authUser->isEmployee()) {
        
        return redirect()->route('projects.index')
    ->with('error', 'You are not allowed to create projects.');
    }

    // ✅ Validate
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|in:Active,Completed,On Hold',
        'start_date' => 'required|date',
        'due_date' => 'required|date|after:start_date',
        'team_id' => 'nullable|exists:teams,id',
        'member_ids' => 'nullable|array',
        'member_ids.*' => 'exists:users,id',
    ]);

    // ✅ TEAM VALIDATION FIRST (before create)

    // Team Leader restriction
    if ($authUser->isTeamLeader()) {
        $team = Team::where('leader_id', $authUser->id)->first();

        if (!$team) {
            abort(403, 'You do not have a team.');
        }

        // ❗ Force correct team_id
        if (empty($validated['team_id']) || $validated['team_id'] != $team->id) {
            abort(403, 'You can only create projects for your own team.');
        }
    }

    // ✅ Validate members belong to team
    if (!empty($validated['team_id']) && !empty($validated['member_ids'])) {
        $teamMembers = Team::find($validated['team_id'])
            ->members()
            ->pluck('users.id')
            ->toArray();

        foreach ($validated['member_ids'] as $userId) {
            if (!in_array($userId, $teamMembers)) {
                abort(403, 'You can only assign members from your team.');
            }
        }
    }

    // ✅ NOW SAFE TO CREATE
    $project = Project::create([
        'name' => $validated['name'],
        'description' => $validated['description'],
        'status' => $validated['status'],
        'start_date' => $validated['start_date'],
        'due_date' => $validated['due_date'],
        'progress' => 0,
        'team_id' => $validated['team_id'],
        'created_by' => $authUser->id,
    ]);

    // ✅ Attach members
    if (!empty($validated['member_ids'])) {
        foreach ($validated['member_ids'] as $userId) {
            $project->members()->attach($userId, ['status' => 'pending']);

            $user = User::find($userId);
            if ($user) {
                $user->notify(new \App\Notifications\ProjectInvite($project));
            }
        }
    }

    // ✅ Default columns
    foreach (['To Do', 'In Progress', 'Review', 'Completed'] as $index => $name) {
        $project->columns()->create([
            'name' => $name,
            'order' => $index,
        ]);
    }

    if ($request->expectsJson()) {
    return response()->json([
        'success' => true,
        'message' => 'Project created successfully!',
        'project' => $project
    ]);
}

    return redirect()->route('projects.index')
    ->with('success', 'Project created successfully!');
}

    public function show($id)
    {
        $project = Project::with('team.members', 'creator', 'members', 'tasks')->findOrFail($id);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin') {
            $isCreator = $project->created_by === $user->id;
            $isMember = $project->members()->where('users.id', $user->id)->where('project_user.status', 'accepted')->exists();
            $isTeamLead = $user->role === 'Team Leader' && $project->team_id && \App\Models\Team::where('id', $project->team_id)->where('leader_id', $user->id)->exists();
            
            if (!$isCreator && !$isMember && !$isTeamLead) {
                // Check if they are pending to redirect to invitation
                if ($project->members()->where('users.id', $user->id)->where('project_user.status', 'pending')->exists()) {
                    return redirect()->route('projects.invitation', $id);
                }
                abort(403);
            }
        }

        $tasks = $project->tasks;
        
        $todoTasks = $tasks->where('status', 'To Do');
        $inProgressTasks = $tasks->where('status', 'In Progress');
        $reviewTasks = $tasks->where('status', 'Review');
        $completedTasks = $tasks->where('status', 'Completed');

        $user = auth()->user();
        $teamMembers = collect();
        
        if ($user->role === 'Team Leader') {
            $myTeam = \App\Models\Team::where('leader_id', $user->id)->first();
            $teamMembers = $myTeam ? $myTeam->members : collect();
        } elseif ($project->team) {
            $teamMembers = $project->team->members;
        } else {
            $teamMembers = \App\Models\User::all();
        }

        $currentMemberIds = $project->members->pluck('id')->toArray();

        return view('projects.show', compact('project', 'tasks', 'todoTasks', 'inProgressTasks', 'reviewTasks', 'completedTasks', 'teamMembers', 'currentMemberIds'));
    }

    public function board($boardId)
    {
        $project = Project::with('team', 'members', 'columns')->findOrFail($boardId);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin') {
            $isCreator = $project->created_by === $user->id;
            $isMember = $project->members()->where('users.id', $user->id)->where('project_user.status', 'accepted')->exists();
            $isTeamLead = $user->role === 'Team Leader' && $project->team_id && \App\Models\Team::where('id', $project->team_id)->where('leader_id', $user->id)->exists();
            
            if (!$isCreator && !$isMember && !$isTeamLead) {
                if ($project->members()->where('users.id', $user->id)->where('project_user.status', 'pending')->exists()) {
                    return redirect()->route('projects.invitation', $boardId);
                }
                abort(403);
            }
        }

        $tasksQuery = Task::where('project_id', $project->id)->with('assignee');

        // Employees only see tasks assigned to them
        if ($user->role === 'Employee') {
            $tasksQuery->where('assigned_to', $user->id);
        }

        $tasks = $tasksQuery->get();
        $projectMembers = $project->members;
        $columns = $project->columns;

        return view('kanban.board', compact('project', 'tasks', 'projectMembers', 'columns'));
    }

    public function addColumn(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin' && $project->created_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $lastOrder = $project->columns()->max('order') ?? -1;

        $project->columns()->create([
            'name' => $validated['name'],
            'order' => $lastOrder + 1,
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Column added successfully!']);
        }

        return back()->with('success', 'Column added successfully!');
    }

    public function updateColumn(Request $request, $id, $columnId)
    {
        $project = Project::findOrFail($id);
        $column = ProjectColumn::where('project_id', $id)->findOrFail($columnId);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin' && $project->created_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // If name changed, we might want to update task statuses too?
        // Since tasks.status is a string matching column name
        if ($column->name !== $validated['name']) {
            Task::where('project_id', $id)->where('status', $column->name)->update(['status' => $validated['name']]);
        }

        $column->update(['name' => $validated['name']]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Column updated successfully!']);
        }

        return back()->with('success', 'Column updated successfully!');
    }

    public function deleteColumn(Request $request, $id, $columnId)
    {
        $project = Project::findOrFail($id);
        $column = ProjectColumn::where('project_id', $id)->findOrFail($columnId);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin' && $project->created_by !== $user->id) {
            abort(403);
        }

        // Move tasks to the first available column if exists
        $firstColumn = ProjectColumn::where('project_id', $id)->where('id', '!=', $columnId)->orderBy('order')->first();
        
        if ($firstColumn) {
            Task::where('project_id', $id)->where('status', $column->name)->update(['status' => $firstColumn->name]);
        } else {
            // If no other column, maybe don't allow delete or just leave tasks with old status
            // For now, let's just delete the column.
        }

        $column->delete();

        // Reorder remaining columns
        $project->columns()->orderBy('order')->get()->each(function($col, $index) {
            $col->update(['order' => $index]);
        });

        if ($request->ajax()) {
            return response()->json(['message' => 'Column deleted successfully!']);
        }

        return back()->with('success', 'Column deleted successfully!');
    }

    public function reorderColumns(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin' && $project->created_by !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:project_columns,id',
        ]);

        foreach ($validated['orders'] as $index => $columnId) {
            ProjectColumn::where('project_id', $id)->where('id', $columnId)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $project = Project::with('team', 'members')->findOrFail($id);
        $teams = \App\Models\Team::with('members')->get();
        return view('projects.edit', compact('project', 'teams'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Completed,On Hold',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after:start_date',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'team_id' => $validated['team_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project updated successfully!']);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        // Delete associated tasks first
        $project->tasks()->delete();
        
        // Detach members
        $project->members()->detach();
        
        // Delete project
        $project->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project deleted successfully!', 'redirect' => route('projects.index')]);
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }

    public function addMember(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // Optional: verify users are in the team
        if ($project->team) {
            $teamMemberIds = $project->team->members->pluck('id')->toArray();
            foreach ($validated['user_ids'] as $userId) {
                if (!in_array($userId, $teamMemberIds)) {
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => 'One or more users are not in the project\'s team'], 403);
                    }
                    return back()->with('error', 'One or more users are not in the project\'s team.');
                }
            }
        }

        foreach ($validated['user_ids'] as $userId) {
            // Only add if not already a member
            if (!$project->members()->where('users.id', $userId)->exists()) {
                $project->members()->attach($userId, ['status' => 'pending']);
                
                // Notify user
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->notify(new \App\Notifications\ProjectInvite($project));
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Invitations sent successfully!', 'redirect' => route('projects.show', $project->id)]);
        }

        return back()->with('success', 'Invitations sent successfully!');
    }

    public function invitation($id)
    {
        $project = Project::with('creator', 'members')->findOrFail($id);
        $user = auth()->user();

        // Verify user is invited and status is pending
        $membership = $project->members()->where('users.id', $user->id)->first();
        if (!$membership || $membership->pivot->status !== 'pending') {
            return redirect()->route('projects.show', $id);
        }

        return view('projects.invitation', compact('project'));
    }

    public function acceptInvite(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = auth()->user();

        $project->members()->updateExistingPivot($user->id, ['status' => 'accepted']);

        // Mark notification as read
        $notificationId = $request->input('notification_id');
        if ($notificationId) {
            $user->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project invitation accepted!', 'redirect' => route('projects.show', $project->id)]);
        }

        return redirect()->route('projects.show', $project->id)->with('success', 'Project invitation accepted!');
    }

    public function rejectInvite(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $user = auth()->user();

        $project->members()->detach($user->id);

        // Mark notification as read
        $notificationId = $request->input('notification_id');
        if ($notificationId) {
            $user->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project invitation rejected!']);
        }

        return back()->with('success', 'Project invitation rejected!');
    }

    public function syncMembers(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        $newMemberIds = $validated['member_ids'] ?? [];
        $currentMemberIds = $project->members->pluck('id')->toArray();
        
        // Members to add (not in current)
        $toAdd = array_diff($newMemberIds, $currentMemberIds);
        
        // Members to remove (in current but not in new)
        $toRemove = array_diff($currentMemberIds, $newMemberIds);

        // Add new members as pending and notify
        foreach ($toAdd as $userId) {
            $project->members()->attach($userId, ['status' => 'pending']);
            
            $user = \App\Models\User::find($userId);
            if ($user) {
                $user->notify(new \App\Notifications\ProjectInvite($project));
            }
        }

        // Remove members
        if (!empty($toRemove)) {
            $project->members()->detach($toRemove);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project members updated and invitations sent!', 'redirect' => route('projects.show', $project->id)]);
        }

        return back()->with('success', 'Project members updated and invitations sent!');
    }

    public function removeMember(Request $request, $id, $userId)
    {
        $project = Project::findOrFail($id);
        $project->members()->detach($userId);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Member removed successfully!', 'redirect' => route('projects.show', $project->id)]);
        }

        return back()->with('success', 'Member removed successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Active,Completed,On Hold',
        ]);

        $project->update([
            'status' => $validated['status']
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project status updated successfully!', 'redirect' => route('projects.show', $project->id)]);
        }

        return back()->with('success', 'Project status updated successfully!');
    }
}
