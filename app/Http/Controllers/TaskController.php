<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\TaskActivity;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|string|max:255',
            'column_id' => 'required|exists:columns,id',
        ]);

        $column = \App\Models\Column::findOrFail($validated['column_id']);

$task = $column->tasks()->create([
    'title' => $validated['title'],
    'description' => $validated['description'] ?? null,
    'priority' => $validated['priority'],
    'due_date' => $validated['due_date'] ?? null,
    'assigned_to' => $validated['assigned_to'] ?? null,

    'created_by' => Auth::guard('web')->check() ? Auth::guard('web')->id() : null,
    'member_id' => Auth::guard('member')->check() ? Auth::guard('member')->id() : null,

    'project_id' => $project->id,
    'team_id' => Auth::user()->team_id ?? $project->team_id,
]);

$this->logActivity(
    $task->id,
    'created',
    auth()->user()->name . ' created this task'
);

return redirect()->route('projects.show', $project->id);
    }

    
    public function destroy(Task $task)
    {
        if (Auth::guard('member')->check()) {
            abort(403, 'Members cannot delete tasks.');
        }

        $task->delete();
        return back();
    }
public function show(Task $task)
{
    $task->load(['activities.user', 'comments.user', 'comments.member']);

    return response()->json([
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => $task->priority,
        'due_date' => $task->due_date,
        'assigned_to' => $task->assigned_to,
        'assigned_to_name' => $task->assigned ? $task->assigned->name : ($task->assigned_to ?? null),
        'creator_name' => $task->creator->name ?? ($task->memberCreator->name ?? null),

        'activities' => $task->activities->map(function ($a) {
            return [
                'action' => $a->action,
                'description' => $a->description,
                'user' => optional($a->user)->name,
                'date' => $a->created_at->diffForHumans(),
            ];
        }),

        'comments' => $task->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'author' => $comment->authorName(),
                'content' => $comment->content,
                'date' => $comment->created_at->diffForHumans(),
            ];
        }),
    ]);
}
    
    public function update(Request $request, Task $task)
    {
        if (Auth::guard('member')->check()) {
            abort(403, 'Members cannot edit tasks.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|string|max:255',
        ]);

        $task->update($validated);

        return back();
    }
    
public function updateStatus(Request $request, Task $task)
{
    $validated = $request->validate([
        'column_id' => 'required|exists:columns,id',
    ]);

    $oldColumn = $task->column->name;

    $newColumn = \App\Models\Column::findOrFail($validated['column_id'])->name;

    $task->update([
        'column_id' => $validated['column_id']
    ]);

    $this->logActivity(
        $task->id,
        'moved',
        auth()->user()->name . " moved task from $oldColumn to $newColumn"
    );

    return response()->json(['success' => true]);
}
 private function logActivity($taskId, $action, $description)
{
    TaskActivity::create([
        'task_id' => $taskId,
        'user_id' => auth()->id(),
        'action' => $action,
        'description' => $description,
    ]);
}
}
