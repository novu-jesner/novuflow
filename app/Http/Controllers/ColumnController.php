<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Project;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $position = $project->columns()->max('position') + 1;

        $project->columns()->create([
            'name' => $validated['name'],
            'position' => $position,
        ]);

        return back();
    }

    public function update(Request $request, Column $column)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $column->update(['name' => $validated['name']]);

        return back();
    }

    public function destroy(Column $column)
    {
        $column->delete();

        return back();
    }

    public function reorder(Request $request, Project $project)
    {
        $validated = $request->validate([
            'columns' => 'required|array',
            'columns.*' => 'exists:columns,id',
        ]);

        foreach ($validated['columns'] as $index => $columnId) {
            Column::where('id', $columnId)
                  ->where('project_id', $project->id)
                  ->update(['position' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
