<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        \App\Models\Project::create([
            'name' => $validated['name'],
            'team_id' => auth()->user()->team_id ?? null,
        ]);

        return back()->with('success', 'Project created successfully.');
    }
}
