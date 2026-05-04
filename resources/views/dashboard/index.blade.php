@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div>
        <h1 class="text-3xl font-semibold text-gray-900">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-600 mt-1">
            Here's what's happening with your projects today.
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Total Projects</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <path d="M3 3h18v18H3z"></path>
                    <path d="M9 3v18"></path>
                    <path d="M3 9h18"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['total_projects'] }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    Active projects
                </p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Active Tasks</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['active_tasks'] }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    Currently active
                </p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Completed</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['completed_tasks'] }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    Tasks completed
                </p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Team Members</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $stats['team_members_count'] }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    Active members
                </p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Active Projects -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">Active Projects</h2>
                <p class="text-sm text-gray-600">Your current projects and their progress</p>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @forelse($projects->take(3) as $project)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $project->name }}</div>
                                <div class="text-sm text-gray-500">Due: {{ $project->due_date ? $project->due_date->format('M d, Y') : 'No due date' }}</div>
                            </div>
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">{{ $project->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                @foreach($project->members->take(3) as $member)
                                <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ substr($member->name, 0, 1) }}</div>
                                @endforeach
                            </div>
                            @if($project->members->count() > 3)
                            <span class="text-xs text-gray-500">+{{ $project->members->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>No active projects</p>
                    </div>
                    @endforelse

                    <a href="{{ url('/dashboard/projects') }}" class="block w-full text-center py-2 px-4 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        View All Projects
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">Recent Activity</h2>
                <p class="text-sm text-gray-600">Latest updates from your team</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($tasks->sortByDesc('updated_at')->take(4) as $task)
                    @php
                        $activityUser = $task->updated_at != $task->created_at && $task->updater 
                            ? $task->updater 
                            : ($task->creator ?? null);
                    @endphp
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-sm">
                            {{ $activityUser ? substr($activityUser->name, 0, 1) : 'S' }}
                        </div>
                        <div class="flex-1 space-y-1">
                            <p class="text-sm">
                                <span class="font-medium">{{ $activityUser->name ?? 'System' }}</span>
                                @if($task->updated_at != $task->created_at)
                                    updated task status to {{ $task->status }}
                                @else
                                    created task
                                @endif
                                <span class="text-[#3f8caf]">"{{ $task->title }}"</span>
                            </p>
                            <p class="text-xs text-gray-500">{{ $task->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- My Tasks Summary -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">My Tasks</h2>
                <p class="text-sm text-gray-600">Your assigned tasks overview</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($tasks->take(4) as $task)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-2 w-2 rounded-full @if($task->priority === 'High') bg-red-500 @elseif($task->priority === 'Medium') bg-orange-500 @else bg-green-500 @endif"></div>
                            <div>
                                <div class="text-sm font-medium">{{ $task->title }}</div>
                                <div class="text-xs text-gray-500">Due: {{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}</div>
                            </div>
                        </div>
                        <span class="px-2 py-1 @if($task->priority === 'High') bg-red-100 text-red-700 @elseif($task->priority === 'Medium') bg-gray-100 text-gray-700 @else bg-green-100 text-green-700 @endif text-xs rounded-full">{{ $task->priority }}</span>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>No tasks assigned to you</p>
                    </div>
                    @endforelse

                    <a href="{{ url('/dashboard/my-tasks') }}" class="block w-full text-center py-2 px-4 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        View All Tasks
                    </a>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        @if(auth()->user()->role === 'SuperAdmin' || auth()->user()->role === 'Admin' || auth()->user()->role === 'Team Leader')
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">Team Performance</h2>
                <p class="text-sm text-gray-600">Overview of team member activity</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($teamMembers as $member)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3f8caf] to-[#54acc8] border-2 border-white flex items-center justify-center text-white text-sm">
                                {{ $member->name ? $member->name[0] : 'U' }}
                            </div>
                            <div>
                                <div class="text-sm font-medium">{{ $member->name }}</div>
                                <div class="text-xs text-gray-500">{{ $member->role }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-green-600">{{ $member->dashboard_completed_tasks }} completed</div>
                            <div class="text-xs text-gray-500">{{ $member->dashboard_active_tasks }} active</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <p>No team members found</p>
                    </div>
                    @endforelse

                    <a href="{{ url('/dashboard/team') }}" class="block w-full text-center py-2 px-4 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        View Team Details
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
