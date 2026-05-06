<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
    /**
     * Handle notification redirection and marking as read.
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        
        $notification->markAsRead();
        
        $data = $notification->data;
        $type = $data['type'] ?? '';
        
        switch ($type) {
            case 'task_commented':
            case 'task_assigned':
                $task = \App\Models\Task::find($data['task_id']);
                if (!$task) {
                    return redirect()->route('notifications.index')->with('error', 'The task associated with this notification has been deleted.');
                }
                $anchor = isset($data['comment_id']) ? '#comment-' . $data['comment_id'] : '';
                return redirect(route('tasks.show', $data['task_id']) . $anchor);
                
            case 'project_invite':
                $project = \App\Models\Project::find($data['project_id']);
                if (!$project) {
                    return redirect()->route('notifications.index')->with('error', 'The project associated with this notification has been deleted.');
                }
                return redirect()->route('projects.invitation', $data['project_id']);
                
            default:
                return redirect()->route('notifications.index');
        }
    }
}
