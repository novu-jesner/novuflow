<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = \App\Models\Project::latest()->get();

        if (view()->exists('projects.index')) {
            return view('projects.index', compact('projects'));
        }
        return back();
    }

    public function show(\App\Models\Project $project)
    {
        $project->load(['columns.tasks' => function ($query) {
            $query->orderBy('created_at'); // or order by position if tasks are ordered
        }]);

        // If the project has no columns, let's create defaults for convenience.
        if ($project->columns->isEmpty()) {
            $project->columns()->createMany([
                ['name' => 'To Do', 'position' => 1],
                ['name' => 'In Progress', 'position' => 2],
                ['name' => 'Done', 'position' => 3],
            ]);
            $project->load('columns.tasks');
        }
 
    $users = User::all();

    return view('projects.show', compact('project', 'users'));
     
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        \App\Models\Project::create([
            'name' => $validated['name'],
            'team_id' => Auth::user()->team_id ?? null,
        ]);

        return back()->with('success', 'Project created successfully.');
    }

    public function update(Request $request, \App\Models\Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update(['name' => $validated['name']]);

        return back()->with('success', 'Project updated successfully.');
    }

    public function destroy(\App\Models\Project $project)
    {
        $project->delete();

        return back()->with('success', 'Project deleted successfully.');
    }
}
