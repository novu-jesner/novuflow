<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:To Do,In Progress,Review,Completed',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('kanban.board', $validated['project_id'])->with('success', 'Task added successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:To Do,In Progress,Review,Completed',
        ]);

        $task = Task::findOrFail($id);
        $task->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Task status updated']);
    }

    public function show($id)
    {
        $task = Task::with(['project', 'assignee', 'creator', 'members'])->findOrFail($id);
        return view('tasks.show', compact('task'));
    }

    public function edit($id)
    {
        $task = Task::with(['project', 'assignee', 'creator', 'members'])->findOrFail($id);
        $projects = \App\Models\Project::all();
        $users = \App\Models\User::all();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:To Do,In Progress,Review,Completed',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'],
        ]);

        return redirect()->route('kanban.board', $task->project_id)->with('success', 'Task updated successfully!');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $projectId = $task->project_id;
        
        // Detach members
        $task->members()->detach();
        
        // Delete task
        $task->delete();

        return redirect()->route('kanban.board', $projectId)->with('success', 'Task deleted successfully!');
    }
}
