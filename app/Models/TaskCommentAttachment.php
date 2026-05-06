<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskCommentAttachment extends Model
{
    protected $fillable = ['task_comment_id', 'file_path', 'file_name', 'file_type', 'file_size'];

    public function comment()
    {
        return $this->belongsTo(TaskComment::class, 'task_comment_id');
    }

    public function getUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : '#';
    }

    public function isImage()
    {
        return str_starts_with($this->file_type, 'image/');
    }
}
