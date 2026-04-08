<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'column_id', // Replaced status
        'project_id',
        'assigned_to',
    ];

    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}