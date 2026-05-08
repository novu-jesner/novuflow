<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'project_id',
        'created_by',
        'updated_by',
        'change_type',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Backward compatibility - returns first assignee as single model
    public function assignee()
    {
        return $this->belongsToMany(User::class, 'task_user')->limit(1);
    }
    
    // Get first assignee as single model for backward compatibility
    public function getAssigneeAttribute()
    {
        return $this->assignees->first();
    }
    
    // Alias for members - all assignees
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\TaskComment::class)->with('user')->latest();
    }
}
