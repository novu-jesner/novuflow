<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        if (auth()->user()->role === 'Employee') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|exists:project_columns,name,project_id,' . $request->project_id,
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'exists:users,id',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'project_id' => $validated['project_id'],
            'created_by' => auth()->id(),
        ]);

        // Sync assignees
        if (!empty($validated['assigned_to'])) {
            $task->assignees()->sync($validated['assigned_to']);
            
            // Notify new assignees
            foreach ($validated['assigned_to'] as $userId) {
                if ($userId !== auth()->id()) {
                    $assignee = \App\Models\User::find($userId);
                    if ($assignee) {
                        $assignee->notify(new \App\Notifications\TaskAssigned($task));
                    }
                }
            }
        }

        $task->load('assignees');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task added successfully!', 'task' => $task]);
        }

        return redirect()->route('kanban.board', $validated['project_id'])->with('success', 'Task added successfully!');
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|exists:project_columns,name,project_id,' . $task->project_id,
        ]);
        
        if (!$this->authorizeStatusUpdate($task)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $task->update([
            'status' => $validated['status'],
            'updated_by' => auth()->id()
        ]);

        return response()->json(['success' => true, 'message' => 'Task status updated']);
    }

    public function show($id)
    {
        $task = Task::with([
            'project', 
            'assignees', 
            'creator', 
            'members', 
            'comments.user', 
            'comments.attachments', 
            'comments.replies.user', 
            'comments.replies.replyTo.user'
        ])->findOrFail($id);
        
        if (!$this->authorizeTaskAction($task)) {
            $user = auth()->user();
            
            // Check if user has pending invitation
            $hasPendingInvitation = $task->project->members()
                ->where('users.id', $user->id)
                ->where('project_user.status', 'pending')
                ->exists();
                
            if ($hasPendingInvitation) {
                return redirect()->route('projects.invitation', $task->project_id)
                    ->with('error', 'You must accept the project invitation first to access this task.');
            }
            
            abort(403);
        }
        
        $user = auth()->user();

        // Determine if the current user can comment
        $canComment = in_array($user->role, ['SuperAdmin', 'Admin'])
            || $task->project->members()
                ->where('users.id', $user->id)
                ->where('project_user.status', 'accepted')
                ->exists();

        return view('tasks.show', compact('task', 'canComment'));
    }

    public function edit($id)
    {
        $task = Task::with(['project.members', 'assignees', 'creator', 'members'])->findOrFail($id);
        
        if (!$this->authorizeTaskAction($task)) {
            abort(403);
        }

        $projects = \App\Models\Project::all();
        $users = $task->project ? $task->project->members : collect();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if (!$this->authorizeTaskAction($task)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|exists:project_columns,name,project_id,' . $task->project_id,
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'exists:users,id',
        ]);

        $oldAssigneeIds = $task->assignees->pluck('id')->toArray();
        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'project_id' => $validated['project_id'],
            'updated_by' => auth()->id(),
        ]);

        // Sync assignees
        if (!empty($validated['assigned_to'])) {
            $task->assignees()->sync($validated['assigned_to']);
            
            // Notify new assignees (those who weren't previously assigned)
            $newAssigneeIds = array_diff($validated['assigned_to'], $oldAssigneeIds);
            foreach ($newAssigneeIds as $userId) {
                if ($userId !== auth()->id()) {
                    $assignee = \App\Models\User::find($userId);
                    if ($assignee) {
                        $assignee->notify(new \App\Notifications\TaskAssigned($task));
                    }
                }
            }
        } else {
            $task->assignees()->detach();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task updated successfully!', 'task' => $task]);
        }

        return redirect()->route('kanban.board', $task->project_id)->with('success', 'Task updated successfully!');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        if (!$this->authorizeTaskAction($task)) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $projectId = $task->project_id;
        
        // Detach assignees
        $task->assignees()->detach();
        
        // Delete task
        $task->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Task deleted successfully!', 'redirect' => route('kanban.board', $projectId)]);
        }

        return redirect()->route('kanban.board', $projectId)->with('success', 'Task deleted successfully!');
    }

    private function authorizeTaskAction(Task $task)
    {
        $user = auth()->user();
        if ($user->role === 'SuperAdmin' || $user->role === 'Admin') {
            return true;
        }

        // Check if user is a member with accepted status
        $isAcceptedMember = $task->project->members()
            ->where('users.id', $user->id)
            ->where('project_user.status', 'accepted')
            ->exists();

        return $isAcceptedMember;
    }

    private function authorizeStatusUpdate(Task $task)
    {
        $user = auth()->user();
        if ($user->role === 'SuperAdmin' || $user->role === 'Admin') {
            return true;
        }

        // Check if user is a member with accepted status
        $isAcceptedMember = $task->project->members()
            ->where('users.id', $user->id)
            ->where('project_user.status', 'accepted')
            ->exists();

        return $isAcceptedMember;
    }
}
