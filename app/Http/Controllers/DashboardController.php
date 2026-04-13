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
            $tasks = Task::with('project', 'assigned', 'column')->get();
            $view = 'super_admin.dashboard';
            break;

        case 'admin':
            $stats = $this->getAdminStats();
            $tasks = Task::with('project', 'assigned', 'column')
                         ->where('team_id', $user->team_id)
                         ->get();
            $view = 'super_admin.dashboard';
            break;

        case 'team_lead':
            $stats = $this->getTeamLeadStats();
            $tasks = Task::with('project', 'assigned', 'column')
                         ->where('team_id', $user->team_id)
                         ->get();
            $view = 'super_admin.dashboard';
            break;

        default:
            $stats = $this->getUserStats();
            $tasks = Task::with('project', 'assigned', 'column')->where('assigned_to', $user->id)->get();
            $view = 'user.dashboard';
    }

    // Temporary fix for undefined variables
    $teams = [];
    $selectedTeamId = null;

    // Return the view dynamically and include sidebar variables for all roles
    return view($view, compact('stats', 'tasks', 'teams', 'selectedTeamId'));
}

    private function getSuperAdminStats()
    {
        return [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'tasks_by_status' => Task::leftJoin('columns', 'tasks.column_id', '=', 'columns.id')
                ->selectRaw("COALESCE(columns.name, 'Unassigned') as status_name, count(*) as count")
                ->groupBy('status_name')
                ->pluck('count', 'status_name')
                ->toArray(),
        ];
    }

    private function getAdminStats()
    {
        $teamId = Auth::user()->team_id;

        return [
            'total_users' => User::where('team_id', $teamId)->count(),
            'total_projects' => Project::where('team_id', $teamId)->count(),
            'total_tasks' => Task::where('team_id', $teamId)->count(),
            'tasks_by_status' => Task::where('team_id', $teamId)
                ->leftJoin('columns', 'tasks.column_id', '=', 'columns.id')
                ->selectRaw("COALESCE(columns.name, 'Unassigned') as status_name, count(*) as count")
                ->groupBy('status_name')
                ->pluck('count', 'status_name')
                ->toArray(),
        ];
    }

    private function getTeamLeadStats()
    {
        $teamId = Auth::user()->team_id;

        return [
            'total_users' => User::where('team_id', $teamId)->count(),
            'total_projects' => Project::where('team_id', $teamId)->count(),
            'total_tasks' => Task::where('team_id', $teamId)->count(),
            'tasks_by_status' => Task::where('team_id', $teamId)
                ->leftJoin('columns', 'tasks.column_id', '=', 'columns.id')
                ->selectRaw("COALESCE(columns.name, 'Unassigned') as status_name, count(*) as count")
                ->groupBy('status_name')
                ->pluck('count', 'status_name')
                ->toArray(),
        ];
    }

    private function getUserStats()
    {
        return [
            'assigned_tasks' => Task::where('assigned_to', Auth::id())->count(),
        ];
    }
}
    
