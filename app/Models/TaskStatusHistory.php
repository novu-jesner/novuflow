<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'old_status',
        'new_status',
        'changed_by_user_id',
        'duration_in_seconds',
    ];

    protected $casts = [
        'duration_in_seconds' => 'integer',
    ];

    public function getDurationLabelAttribute(): string
    {
        return $this->formatDuration($this->duration_in_seconds ?? 0);
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

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
