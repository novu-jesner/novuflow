<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    public function created(Task $task): void
    {
        $task->change_type = 'created';
        $task->saveQuietly();
        $task->project->updateProgress();
    }

    public function updated(Task $task): void
    {
        $changeType = null;
        
        // Determine what changed
        if ($task->isDirty('status')) {
            $changeType = 'status_changed';
        } elseif ($task->isDirty('assigned_to')) {
            $changeType = 'assignee_changed';
        } elseif ($task->isDirty('title')) {
            $changeType = 'title_changed';
        } elseif ($task->isDirty('description')) {
            $changeType = 'description_changed';
        } elseif ($task->isDirty('priority')) {
            $changeType = 'priority_changed';
        } elseif ($task->isDirty('due_date')) {
            $changeType = 'due_date_changed';
        }
        
        if ($changeType) {
            $task->change_type = $changeType;
            $task->saveQuietly();
        }
        
        // Always update progress if status changed
        if ($task->isDirty('status')) {
            $task->project->updateProgress();
        }
    }

    public function deleted(Task $task): void
    {
        $task->project->updateProgress();
    }
}
