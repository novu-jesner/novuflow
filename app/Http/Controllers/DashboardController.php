<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;

class DashboardController extends Controller
{
    public function index()
{
    $user = Auth::user();


    switch ($user->role) {
        case 'super_admin':
            $stats = $this->getSuperAdminStats();
            $tasks = Task::with('project', 'assigned')->get();
            $view = 'super_admin.dashboard';
            break;

        case 'admin':
            $stats = $this->getAdminStats();
            $tasks = Task::with('project', 'assigned')->get();
            $view = 'admin.dashboard';
            break;

        case 'team_lead':
            $stats = $this->getTeamLeadStats();
            $tasks = Task::with('project', 'assigned')
                         ->where('team_id', $user->team_id)
                         ->get();
            $view = 'team_lead.dashboard';
            break;

        default:
            $stats = $this->getUserStats();
            $tasks = Task::where('assigned_to', $user->id)->get();
            $view = 'user.dashboard';
    }

    // Return the view dynamically and include sidebar variables for all roles
    return view($view, compact('stats', 'tasks', 'teams', 'selectedTeamId'));
}

    private function getSuperAdminStats()
    {
        return [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
        ];
    }

    private function getAdminStats()
    {
        return [
            'team_members' => User::where('team_id', Auth::user()->team_id)->count(),
            'projects' => Project::where('team_id', Auth::user()->team_id)->count(),
            'tasks' => Task::where('team_id', Auth::user()->team_id)->count(),
        ];
    }

    private function getTeamLeadStats()
    {
        return [
            'team_members' => User::where('team_id', Auth::user()->team_id)->count(),
            'projects' => Project::where('team_id', Auth::user()->team_id)->count(),
            'tasks' => Task::where('team_id', Auth::user()->team_id)->count(),
        ];
    }

    private function getUserStats()
    {
        return [
            'assigned_tasks' => Task::where('assigned_to', Auth::id())->count(),
        ];
    }
}
    
