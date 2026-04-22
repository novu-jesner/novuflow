<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $project = $task->project;
        $canComment = false;

        // Check access for User (web guard)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'super_admin') {
                $canComment = true;
            } elseif ($user->role === 'admin' && $user->team_id === $project->team_id) {
                $canComment = true;
            } elseif ($user->role === 'team_lead' && $user->id === $project->user_id) {
                $canComment = true;
            }
        } 
        // Check access for Member (member guard)
        elseif (Auth::guard('member')->check()) {
            $member = Auth::guard('member')->user();
            if ($project->members()->where('member_id', $member->id)->exists()) {
                $canComment = true;
            }
        }

        if (!$canComment) {
            abort(403, 'You are not involved in this project.');
        }

        Comment::create([
            'task_id'   => $task->id,
            'user_id'   => Auth::guard('web')->id(),
            'member_id' => Auth::guard('member')->id(),
            'content'   => $request->content,
        ]);

        return back()->with('success', 'Comment added.');
    }
}
