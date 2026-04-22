<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
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
        // Members can only access projects they're explicitly assigned to
        if (auth('member')->check()) {
            $member = auth('member')->user();
            $isMember = $project->members()->where('member_id', $member->id)->exists();
            if (!$isMember) {
                abort(403, 'You are not assigned to this project.');
            }
        }

        $project->load(['columns.tasks' => function ($query) {
            $query->orderBy('created_at');
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
 
        $users   = User::all();
        $members = Member::where('team_id', auth()->user()->team_id ?? null)->get();
        $project->load('members');
        $projectMemberIds = $project->members->pluck('id')->toArray();

        return view('projects.show', compact('project', 'users', 'members', 'projectMemberIds'));
     
    }

    public function syncMembers(Request $request, \App\Models\Project $project)
    {
        $validated = $request->validate([
            'member_ids'   => 'nullable|array',
            'member_ids.*' => 'integer|exists:members,id',
        ]);

        $project->members()->sync($validated['member_ids'] ?? []);

        return back()->with('success', 'Project members updated.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        \App\Models\Project::create([
            'name' => $validated['name'],
            'team_id' => Auth::user()->team_id ?? null,
            'user_id' => Auth::id(),
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
