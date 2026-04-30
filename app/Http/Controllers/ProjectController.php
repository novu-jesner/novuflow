<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('team', 'creator', 'members')->latest()->get();
        return view('projects.index', compact('projects'));
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

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function show($id)
    {
        $project = Project::with('team', 'creator', 'members', 'tasks')->findOrFail($id);
        $tasks = $project->tasks;
        
        $todoTasks = $tasks->where('status', 'To Do');
        $inProgressTasks = $tasks->where('status', 'In Progress');
        $reviewTasks = $tasks->where('status', 'Review');
        $completedTasks = $tasks->where('status', 'Completed');

        return view('projects.show', compact('project', 'tasks', 'todoTasks', 'inProgressTasks', 'reviewTasks', 'completedTasks'));
    }

    public function board($boardId)
    {
        $project = Project::with('team')->findOrFail($boardId);
        $tasks = Task::where('project_id', $project->id)->with('assignee')->get();
        
        $todoTasks = $tasks->where('status', 'To Do');
        $inProgressTasks = $tasks->where('status', 'In Progress');
        $reviewTasks = $tasks->where('status', 'Review');
        $completedTasks = $tasks->where('status', 'Completed');

        return view('kanban.board', compact('project', 'tasks', 'todoTasks', 'inProgressTasks', 'reviewTasks', 'completedTasks'));
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

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
