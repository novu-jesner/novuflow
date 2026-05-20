<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TaskObserver
{
    public function created(Task $task): void
    {
        $task->change_type = 'created';
        $task->saveQuietly();
        $this->recordStatusHistory($task, null, $task->status);
        if ($task->project) {
            $task->project->updateProgress();
        }
    }

    public function updated(Task $task): void
    {
        $changeType = null;
        
        if ($task->wasChanged('status')) {
            $changeType = 'status_changed';
        } elseif ($task->wasChanged('title')) {
            $changeType = 'title_changed';
        } elseif ($task->wasChanged('description')) {
            $changeType = 'description_changed';
        } elseif ($task->wasChanged('priority')) {
            $changeType = 'priority_changed';
        } elseif ($task->wasChanged('due_date')) {
            $changeType = 'due_date_changed';
        }
        // Note: assignee changes are handled in controller since it's a many-to-many relationship
        
        if ($changeType) {
            $task->change_type = $changeType;
            $task->saveQuietly();
        }
        
        if ($task->wasChanged('status')) {
            $this->finalizePreviousStatus($task);
            $this->recordStatusHistory($task, $task->getOriginal('status'), $task->status);
            if ($task->project) {
                $task->project->updateProgress();
            }
        }
    }

    public function deleted(Task $task): void
    {
        if ($task->project) {
            $task->project->updateProgress();
        }
    }

    protected function recordStatusHistory(Task $task, ?string $oldStatus, string $newStatus): void
    {
        $task->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_user_id' => Auth::id() ?? $task->updated_by ?? $task->created_by,
        ]);
    }

    protected function finalizePreviousStatus(Task $task): void
    {
        $previous = $task->statusHistories()
            ->whereNull('duration_in_seconds')
            ->orderByDesc('created_at')
            ->first();

        if (! $previous) {
            return;
        }

        $previous->update([
            'duration_in_seconds' => Carbon::parse($previous->created_at)->diffInSeconds(now()),
        ]);
    }
}
