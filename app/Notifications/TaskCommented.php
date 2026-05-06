<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommented extends Notification
{
    use Queueable;

    protected $task;
    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $comment)
    {
        $this->task = $task;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_commented',
            'title' => 'New Comment on Task',
            'message' => ($this->comment->parent_id ? $this->comment->user->name . ' replied to a comment on: ' : $this->comment->user->name . ' commented on: ') . $this->task->title,
            'comment_body' => $this->comment->body,
            'task_id' => $this->task->id,
            'comment_id' => $this->comment->id,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project->name ?? 'Unknown Project',
        ];
    }
}
