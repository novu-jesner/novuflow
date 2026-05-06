<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskCommentAttachment;
use App\Notifications\TaskCommented;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskCommentController extends Controller
{
    public function store(Request $request, $taskId)
    {
        $task = Task::with(['assignee', 'project', 'members'])->findOrFail($taskId);
        $user = auth()->user();

        // Only Team Leader, Admin, SuperAdmin, or the task's assignee can comment
        if (!$this->canComment($user, $task)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'You are not allowed to comment on this task.'], 403);
            }
            return back()->with('error', 'You are not allowed to comment on this task.');
        }

        $validated = $request->validate([
            'body' => 'nullable|string|max:2000',
            'parent_id' => 'nullable|exists:task_comments,id',
            'reply_to_id' => 'nullable|exists:task_comments,id',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if (empty($validated['body']) && !$request->hasFile('attachments')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Comment or attachment is required.'], 422);
            }
            return back()->with('error', 'Comment or attachment is required.');
        }

        try {
            $comment = TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'parent_id' => $validated['parent_id'] ?? null,
                'reply_to_id' => $validated['reply_to_id'] ?? null,
                'body'    => $validated['body'] ?? '',
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if (!$file->isValid()) {
                        throw new \Exception('File upload failed (Code: ' . $file->getError() . '): ' . $file->getErrorMessage());
                    }
                    // Diagnostic: If realPath is false but pathname exists, try to use pathname
                    $tempPath = $file->getPathname();
                    if (!$tempPath) {
                        throw new \Exception('File upload failed: No temporary path found for ' . $file->getClientOriginalName());
                    }

                    try {
                        $path = $file->store('task-attachments', 'public');
                    } catch (\Throwable $e) {
                        $path = null;
                    }

                    if (!$path) {
                        // Fallback: try to manually put the file
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = 'task-attachments/' . $filename;
                        try {
                            if (!Storage::disk('public')->put($path, file_get_contents($tempPath))) {
                                 throw new \Exception('Could not write to storage');
                            }
                        } catch (\Throwable $e) {
                             throw new \Exception('File upload failed: ' . $e->getMessage() . ' for ' . $file->getClientOriginalName());
                        }
                    }

                    if ($path) {
                        TaskCommentAttachment::create([
                            'task_comment_id' => $comment->id,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }

            $comment->load(['user', 'attachments', 'replies', 'replyTo.user']);

            // Notify involved users
            $involvedUserIds = collect([$task->created_by, $task->assigned_to])
                ->concat($task->members->pluck('id'))
                ->filter(fn($id) => $id && $id !== $user->id)
                ->unique();

            if ($involvedUserIds->isNotEmpty()) {
                $involvedUsers = \App\Models\User::whereIn('id', $involvedUserIds)->get();
                foreach ($involvedUsers as $u) {
                    $u->notify(new TaskCommented($task, $comment));
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'comment' => [
                        'id'         => $comment->id,
                        'parent_id'  => $comment->parent_id,
                        'reply_to_id' => $comment->reply_to_id,
                        'reply_to'   => $comment->replyTo ? [
                            'user' => [
                                'name' => $comment->replyTo->user->name,
                                'initials' => strtoupper(substr($comment->replyTo->user->name, 0, 1)),
                            ],
                            'body' => $comment->replyTo->body,
                        ] : null,
                        'body'       => $comment->body,
                        'created_at' => $comment->created_at->diffForHumans(),
                        'user' => [
                            'name'   => $comment->user->name,
                            'initials' => strtoupper(substr($comment->user->name, 0, 1)),
                        ],
                        'attachments' => $comment->attachments->map(fn($a) => [
                            'id' => $a->id,
                            'name' => $a->file_name,
                            'url' => $a->url,
                            'is_image' => $a->isImage(),
                        ]),
                        'replies' => [],
                        'can_delete' => true,
                    ],
                ]);
            }

            return back()->with('success', 'Comment added.');

        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine()
                ], 500);
            }
            throw $e;
        }
    }

    public function destroy(Request $request, $taskId, $commentId)
    {
        $comment = TaskComment::where('task_id', $taskId)->findOrFail($commentId);
        $user    = auth()->user();

        // Only the comment author, admin, or superadmin can delete
        if ($comment->user_id !== $user->id && !in_array($user->role, ['SuperAdmin', 'Admin', 'Team Leader'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->with('error', 'Unauthorized.');
        }

        // Delete attachments from storage
        foreach ($comment->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment deleted.');
    }

    public function deleteAttachment(Request $request, $attachmentId)
    {
        $attachment = TaskCommentAttachment::with('comment')->findOrFail($attachmentId);
        $user = auth()->user();

        // Only comment author, admin, or team leader can delete
        if ($attachment->comment->user_id !== $user->id && !in_array($user->role, ['SuperAdmin', 'Admin', 'Team Leader'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }

    private function canComment($user, Task $task)
    {
        if (in_array($user->role, ['SuperAdmin', 'Admin', 'Team Leader'])) {
            return true;
        }
        // Employee: only if they are the assignee
        return $task->assigned_to === $user->id;
    }
}
