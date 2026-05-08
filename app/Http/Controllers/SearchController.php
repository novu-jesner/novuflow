<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');

        $tasks = collect();
        $projects = collect();

        if ($query) {
            $tasks = Task::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with('project')
                ->get();

            $projects = Project::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->with('team')
                ->get();
        }

        return view('search.index', compact('tasks', 'projects', 'query'));
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Get task titles
        $taskTitles = Task::where('title', 'like', "%{$query}%")
            ->pluck('title')
            ->take(5)
            ->toArray();

        // Get project names
        $projectNames = Project::where('name', 'like', "%{$query}%")
            ->pluck('name')
            ->take(5)
            ->toArray();

        $suggestions = array_merge($taskTitles, $projectNames);
        $suggestions = array_unique($suggestions);
        $suggestions = array_slice($suggestions, 0, 10); // Limit to 10

        return response()->json($suggestions);
    }
}
