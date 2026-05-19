<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class KanbanAnalyticsController extends Controller
{
    public function projectAnalytics(Request $request, Project $project)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $end = Carbon::parse($request->input('end_date', now()))->endOfDay();
        $start = Carbon::parse($request->input('start_date', now()->subDays(7)))->startOfDay();

        $histories = $project->tasks()
            ->with(['statusHistories.changedBy'])
            ->get()
            ->flatMap(fn ($task) => $task->statusHistories)
            ->filter(fn ($history) => $history->created_at->between($start, $end));

        $timeline = $this->buildTimeline($histories, $start, $end);
        $heatmap = $this->buildHeatmap($project); 

        return response()->json([
            'project_id' => $project->id,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'timeline' => $timeline,
            'heatmap' => $heatmap,
        ]);
    }

    protected function buildTimeline(Collection $histories, Carbon $start, Carbon $end): array
    {
        return $histories
            ->sortBy('created_at')
            ->map(function ($history) {
                return [
                    'task_id' => $history->task_id,
                    'old_status' => $history->old_status,
                    'new_status' => $history->new_status,
                    'changed_at' => $history->created_at->toDateTimeString(),
                    'changed_by' => $history->changedBy?->name,
                ];
            })
            ->groupBy(fn ($item) => Carbon::parse($item['changed_at'])->format('Y-m-d H:00'))
            ->map(fn ($entries, $bucket) => [
                'bucket' => $bucket,
                'events' => $entries,
                'counts' => collect($entries)->countBy('new_status')->toArray(),
            ])
            ->values()
            ->toArray();
    }

    protected function buildHeatmap(Project $project): array
    {
        $durations = $project->tasks()
            ->with('statusHistories')
            ->get()
            ->flatMap(fn ($task) => $task->statusHistories)
            ->groupBy('new_status')
            ->map(fn ($group) => $group->sum('duration_in_seconds'))
            ->toArray();

        $columns = ['To Do', 'In Progress', 'Ready for Review', 'Completed'];

        return collect($columns)
            ->map(fn ($column) => [
                'column' => $column,
                'duration_seconds' => $durations[$column] ?? 0,
                'duration_label' => $this->formatDuration($durations[$column] ?? 0),
                'intensity' => $this->heatmapIntensity($durations[$column] ?? 0),
                'color' => $this->heatmapColor($durations[$column] ?? 0),
            ])
            ->toArray();
    }

    protected function heatmapIntensity(int $seconds): string
    {
        if ($seconds >= 43200) {
            return 'critical';
        }

        if ($seconds >= 14400) {
            return 'warning';
        }

        return 'safe';
    }

    protected function heatmapColor(int $seconds): string
    {
        return match ($this->heatmapIntensity($seconds)) {
            'critical' => 'bg-red-500/20 ring-red-500/40',
            'warning' => 'bg-orange-400/20 ring-orange-400/35',
            default => 'bg-emerald-400/15 ring-emerald-500/20',
        };
    }

    protected function formatDuration(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds === 1 ? '1 second' : "{$seconds} seconds";
        }

        if ($seconds < 3600) {
            $minutes = (int) round($seconds / 60);
            return $minutes === 1 ? '1 minute' : "{$minutes} minutes";
        }

        if ($seconds < 86400) {
            $hours = (int) round($seconds / 3600);
            return $hours === 1 ? '1 hour' : "{$hours} hours";
        }

        if ($seconds < 604800) {
            $days = (int) round($seconds / 86400);
            return $days === 1 ? '1 day' : "{$days} days";
        }

        if ($seconds < 2629746) {
            $weeks = (int) round($seconds / 604800);
            return $weeks === 1 ? '1 week' : "{$weeks} weeks";
        }

        if ($seconds < 31556952) {
            $months = (int) round($seconds / 2629746);
            return $months === 1 ? '1 month' : "{$months} months";
        }

        $years = (int) round($seconds / 31556952);
        return $years === 1 ? '1 year' : "{$years} years";
    }
}
