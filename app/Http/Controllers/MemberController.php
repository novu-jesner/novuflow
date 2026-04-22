<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index()
    {
        $user = Auth::user();

        // Super admins see all members, others see only their team
        if ($user->role === 'super_admin') {
            $members = Member::latest()->get();
        } else {
            $members = Member::where('team_id', $user->team_id)
                ->latest()
                ->get();
        }

        return view('members.index', compact('members'));
    }

    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:members,email',
            'password' => 'required|string|min:6',
            'position' => 'nullable|string|max:255',
            'team_id'  => 'nullable|integer',
        ]);

        // Create the member profile directly
        Member::create([
            'team_id'  => $validated['team_id'] ?? Auth::user()->team_id,
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'position' => $validated['position'] ?? null,
        ]);

        return back()->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified member.
     */
    public function show(Member $member)
    {
        $member->load('tasks');

        return view('members.show', compact('member'));
    }

    /**
     * Update the specified member.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:members,email,'.$member->id,
            'position' => 'nullable|string|max:255',
            'team_id'  => 'nullable|integer',
        ]);

        $member->update($validated);

        return back()->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified member.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return back()->with('success', 'Member removed successfully.');
    }
}
