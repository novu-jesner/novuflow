<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('leader', 'members')->latest()->get();
        $members = User::with('teams')->latest()->get();
        
        return view('team.index', compact('teams', 'members'));
    }

    public function adminTeams()
    {
        $teams = Team::with('leader', 'members', 'projects')->latest()->get();
        $totalTeams = $teams->count();
        $totalMembers = User::count();
        $activeProjects = 0;
        foreach ($teams as $team) {
            $activeProjects += $team->projects()->where('status', 'Active')->count();
        }
        $avgTeamSize = $totalTeams > 0 ? round($totalMembers / $totalTeams) : 0;

        return view('admin.teams', compact('teams', 'totalTeams', 'totalMembers', 'activeProjects', 'avgTeamSize'));
    }
}
