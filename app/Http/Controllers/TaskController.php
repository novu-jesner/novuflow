<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'column_id' => 'required|exists:columns,id',
        ]);

        $project->tasks()->create([
            'title' => $validated['title'],
            'column_id' => $validated['column_id'],
        ]);

        return back();
    }

    public function updateStatus(Request $request, Task $task)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
        ]);

        $task->update(['column_id' => $validated['column_id']]);

        return response()->json(['success' => true]);
    }
    
    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }
}
