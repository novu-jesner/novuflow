<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'parent_id', 'reply_to_id', 'body'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(TaskComment::class, 'parent_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(TaskComment::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(TaskComment::class, 'parent_id')->with('user');
    }

    public function attachments()
    {
        return $this->hasMany(TaskCommentAttachment::class);
    }
}
