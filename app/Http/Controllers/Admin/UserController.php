<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $admins = User::all()->map(function($user) {
            $user->type = 'user';
            return $user;
        });

        $members = Member::all()->map(function($member) {
            $member->type = 'member';
            $member->role = 'member'; // Assign a virtual role for display
            return $member;
        });

        $users = $admins->concat($members)->sortByDesc('created_at');
        $teams = Team::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:super_admin,admin,team_lead,member'],
            'team_id' => ['nullable', 'exists:teams,team_id'],
        ]);

        if ($request->role === 'member') {
            $request->validate(['email' => 'unique:members,email']);
            Member::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'team_id' => $request->team_id,
                'is_active' => true,
            ]);
        } else {
            $request->validate(['email' => 'unique:users,email']);
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'team_id' => $request->team_id,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Account created successfully.');
    }

    public function update(Request $request, $id)
    {
        $type = $request->input('type', 'user');
        $model = $type === 'member' ? Member::findOrFail($id) : User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.($type === 'member' ? 'members' : 'users').',email,'.$id],
            'is_active' => ['required', 'boolean'],
            'team_id' => ['nullable', 'exists:teams,team_id'],
        ]);

        if ($type === 'member' && $request->role !== 'member') {
            // Promotion logic: Migrate Member to User table
            if (User::where('email', $model->email)->exists()) {
                return back()->with('error', 'A system user with this email already exists.');
            }

            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $model->password, // Transfer hashed password
                'role' => $request->role,
                'is_active' => $request->is_active,
                'team_id' => $request->team_id ?? $model->team_id,
            ]);

            $model->delete();
            return back()->with('success', 'Member promoted to system user successfully.');
        }

        // Standard update logic for existing User/Member
        $request->validate(['role' => ['required', 'in:super_admin,admin,team_lead,member']]);
        
        if ($type === 'user') {
            // Prevent super_admin from disabling themselves
            if ($model->id === auth()->id() && !$request->is_active) {
                return back()->with('error', 'You cannot disable your own account.');
            }
            $model->role = $request->role;
        }

        $model->name = $request->name;
        $model->email = $request->email;
        $model->is_active = $request->is_active;
        $model->team_id = $request->team_id;

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $model->password = Hash::make($request->password);
        }

        $model->save();

        return back()->with('success', 'Account updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $type = $request->input('type', 'user');
        
        if ($type === 'user' && $id == auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $model = $type === 'member' ? Member::findOrFail($id) : User::findOrFail($id);
        $model->delete();

        return back()->with('success', 'Account deleted successfully.');
    }

    public function storeTeam(Request $request)
    {
        $request->validate([
            'team_id' => ['required', 'integer', 'unique:teams,team_id'],
            'name' => ['required', 'string', 'max:255', 'unique:teams,name'],
        ]);

        Team::create([
            'team_id' => $request->team_id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Team created successfully.');
    }
}
