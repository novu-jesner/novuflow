@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ searchQuery: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Team</h1>
            <p class="text-gray-600 mt-1">Manage your team members and their roles</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="20" x2="20" y1="8" y2="14"></line>
                <line x1="23" x2="17" y1="11" y2="11"></line>
            </svg>
            Invite Member
        </a>
    </div>

    <!-- Search -->
    <div class="relative max-w-md">
        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
        </svg>
        <input
            type="text"
            placeholder="Search team members..."
            x-model="searchQuery"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
        >
    </div>

    @php
        $totalMembers = $members->count();
        $activeTasks = \App\Models\Task::whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
        $completedTasks = \App\Models\Task::where('status', 'Completed')->count();
        $avgCompletion = $activeTasks + $completedTasks > 0 ? round(($completedTasks / ($activeTasks + $completedTasks)) * 100) : 0;
    @endphp
    <!-- Team Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Members</h3>
            <div class="text-2xl font-bold">{{ $totalMembers }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Active Tasks</h3>
            <div class="text-2xl font-bold">{{ $activeTasks }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Completed Tasks</h3>
            <div class="text-2xl font-bold">{{ $completedTasks }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Avg. Completion Rate</h3>
            <div class="text-2xl font-bold">{{ $avgCompletion }}%</div>
        </div>
    </div>

    <!-- Team Members List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="font-semibold">Team Members</h2>
            <p class="text-sm text-gray-600">All members in your organization</p>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($members as $member)
                @php
                    $tasksCompleted = \App\Models\Task::where('assigned_to', $member->id)->where('status', 'Completed')->count();
                    $activeTasks = \App\Models\Task::where('assigned_to', $member->id)->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
                @endphp
                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors" 
                     x-data="{ show: true }" 
                     x-show="show && ('{{ strtolower($member->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($member->email) }}'.includes(searchQuery.toLowerCase()))">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white font-semibold">{{ substr($member->name, 0, 1) }}</div>
                        <div>
                            <div class="font-medium">{{ $member->name }}</div>
                            <div class="text-sm text-gray-500 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                                <span>{{ $member->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="px-2 py-1 text-xs rounded-full text-white
                            @if($member->role == 'SuperAdmin') bg-purple-500
                            @elseif($member->role == 'Admin') bg-blue-500
                            @elseif($member->role == 'Team Leader') bg-green-500
                            @else bg-gray-500 @endif">{{ $member->role }}</span>
                    <div class="text-right">
                        <div class="text-sm font-medium text-green-600">{{ $tasksCompleted }} completed</div>
                        <div class="text-xs text-gray-500">{{ $activeTasks }} active</div>
                    </div>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50">
                                    <a href="{{ route('team.member.profile', $member->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">View Profile</a>
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Change Role</a>
                                    <button type="button" @click="ajaxDelete('{{ route('team.member.remove', $member->id) }}', { onSuccess: () => { show = false; open = false; } })" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Remove Member</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">No team members found</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
