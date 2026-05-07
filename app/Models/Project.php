<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'due_date',
        'progress',
        'team_id',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')->withPivot('status')->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function calculateProgress(): int
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return 0;
        }
        
        $completedTasks = $this->tasks()->where('status', 'Completed')->count();
        
        return (int) round(($completedTasks / $totalTasks) * 100);
    }

    public function updateProgress(): void
    {
        $this->progress = $this->calculateProgress();
        $this->save();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function columns()
    {
        return $this->hasMany(ProjectColumn::class)->orderBy('order');
    }

    public function comments()
    {
        return $this->hasManyThrough(TaskComment::class, Task::class);
    }
}
