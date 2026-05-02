<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
