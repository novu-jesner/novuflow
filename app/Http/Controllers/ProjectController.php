<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\ProjectColumn;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Project::with('team', 'creator', 'members');

        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin') {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('members', function($q) use ($user) {
                      $q->where('users.id', $user->id);
                  });
                
                if ($user->role === 'Team Leader') {
                    $teamIds = \App\Models\Team::where('leader_id', $user->id)->pluck('id');
                    $q->orWhereIn('team_id', $teamIds);
                }
            });
        }

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
        $teams = \App\Models\Team::with('members')->get();
        return view('projects.create', compact('teams'));
    }

    public function store(Request $request)
    {
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

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'progress' => 0,
            'team_id' => $validated['team_id'],
            'created_by' => auth()->id(),
        ]);

        if (!empty($validated['member_ids'])) {
            $project->members()->attach($validated['member_ids']);
        }

        // Create default columns
        $defaultColumns = ['To Do', 'In Progress', 'Review', 'Completed'];
        foreach ($defaultColumns as $index => $name) {
            $project->columns()->create([
                'name' => $name,
                'order' => $index,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project created successfully!', 'redirect' => route('projects.index')]);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function show($id)
    {
        $project = Project::with('team.members', 'creator', 'members', 'tasks')->findOrFail($id);
        
        $user = auth()->user();
        if ($user->role !== 'SuperAdmin' && $user->role !== 'Admin') {
            $isCreator = $project->created_by === $user->id;
            $isMember = $project->members->contains($user->id);
            $isTeamLead = $user->role === 'Team Leader' && $project->team_id && \App\Models\Team::where('id', $project->team_id)->where('leader_id', $user->id)->exists();
            
            if (!$isCreator && !$isMember && !$isTeamLead) {
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
            $isMember = $project->members->contains($user->id);
            $isTeamLead = $user->role === 'Team Leader' && $project->team_id && \App\Models\Team::where('id', $project->team_id)->where('leader_id', $user->id)->exists();
            
            if (!$isCreator && !$isMember && !$isTeamLead) {
                abort(403);
            }
        }

        $tasks = Task::where('project_id', $project->id)->with('assignee')->get();
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

        $project->members()->syncWithoutDetaching($validated['user_ids']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Members added successfully!', 'redirect' => route('projects.show', $project->id)]);
        }

        return back()->with('success', 'Members added successfully!');
    }

    public function syncMembers(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $validated = $request->validate([
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id'
        ]);

        $project->members()->sync($validated['member_ids'] ?? []);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project members updated successfully!', 'redirect' => route('projects.show', $project->id)]);
        }

        return back()->with('success', 'Project members updated successfully!');
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
