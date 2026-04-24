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
}
