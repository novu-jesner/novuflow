<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
   protected $fillable = [
    'task_id',
    'user_id',
    'action',
    'description',
];
public function user()
{
    return $this->belongsTo(User::class);
}

public function task()
{
    return $this->belongsTo(Task::class);
}
}
