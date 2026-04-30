@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ url('/dashboard/projects') }}" class="p-2 hover:bg-gray-100 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-semibold text-gray-900">{{ $project->name }}</h1>
                    <span class="px-2 py-1 text-xs rounded-full text-white
                        @if($project->status == 'Planning') bg-gray-500
                        @elseif($project->status == 'In Progress') bg-blue-500
                        @elseif($project->status == 'On Hold') bg-yellow-500
                        @elseif($project->status == 'Completed') bg-green-500
                        @else bg-gray-500 @endif">
                        {{ $project->status }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">{{ $project->description }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.39a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Settings
            </button>
            <a href="{{ route('kanban.board', $project->id) }}" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                View Board
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Progress</h3>
            <div class="text-2xl font-bold">{{ $project->progress }}%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Team Members</h3>
            <div class="text-2xl font-bold">{{ $project->members->count() }}</div>
            <p class="text-xs text-gray-500 mt-1">Active collaborators</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Start Date</h3>
            <div class="text-lg font-medium">{{ $project->start_date->format('M d, Y') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Due Date</h3>
            <div class="text-lg font-medium">{{ $project->due_date->format('M d, Y') }}</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b">
            <button @click="activeTab = 'overview'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'overview' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Overview</button>
            <button @click="activeTab = 'tasks'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'tasks' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Tasks</button>
            <button @click="activeTab = 'team'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'team' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Team</button>
            <button @click="activeTab = 'activity'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'activity' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Activity</button>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Project Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <div>
                                <div class="text-sm font-medium">Start Date</div>
                                <div class="text-sm text-gray-600">{{ $project->start_date->format('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <div>
                                <div class="text-sm font-medium">Due Date</div>
                                <div class="text-sm text-gray-600">{{ $project->due_date->format('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="pt-2">
                            @php
                                $daysRemaining = now()->diffInDays($project->due_date, false);
                            @endphp
                            <div class="text-sm text-gray-600">
                                @if($daysRemaining > 0)
                                    {{ round($daysRemaining) }} days remaining
                                @elseif($daysRemaining == 0)
                                    Due today
                                @else
                                    {{ round(abs($daysRemaining)) }} days overdue
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Quick Stats</h2>
                </div>
                <div class="p-6">
                    @php
                        $totalTasks = $project->tasks->count();
                        $completedTasks = $project->tasks->where('status', 'Completed')->count();
                        $inProgressTasks = $project->tasks->where('status', 'In Progress')->count();
                        $todoTasks = $project->tasks->where('status', 'To Do')->count();
                    @endphp
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Tasks</span>
                            <span class="font-medium">{{ $totalTasks }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Completed</span>
                            <span class="font-medium text-green-600">{{ $completedTasks }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">In Progress</span>
                            <span class="font-medium text-blue-600">{{ $inProgressTasks }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">To Do</span>
                            <span class="font-medium text-gray-600">{{ $todoTasks }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="font-semibold">All Tasks</h2>
                <p class="text-sm text-gray-600">Tasks for this project</p>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @forelse($project->tasks as $task)
                        <div class="flex items-center justify-between p-3 border rounded-md hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full
                                    @if($task->priority == 'Urgent') bg-red-500
                                    @elseif($task->priority == 'High') bg-orange-500
                                    @elseif($task->priority == 'Medium') bg-yellow-500
                                    @else bg-green-500 @endif"></div>
                                <div>
                                    <div class="font-medium">{{ $task->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $task->assignee->name ?? 'Unassigned' }}</div>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($task->priority == 'Urgent') bg-red-100 text-red-700
                                @elseif($task->priority == 'High') bg-orange-100 text-orange-700
                                @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ $task->priority }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No tasks yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Team Tab -->
        <div x-show="activeTab === 'team'" class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="font-semibold">Team Members</h2>
                <p class="text-sm text-gray-600">People working on this project</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($project->members as $member)
                        @php
                            $completedTasks = $member->assignedTasks->where('project_id', $project->id)->where('status', 'Completed')->count();
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white">{{ substr($member->name, 0, 1) }}</div>
                                <div>
                                    <div class="font-medium">{{ $member->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $member->role }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium">{{ $completedTasks }} tasks</div>
                                <div class="text-xs text-gray-500">completed</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No team members yet</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Activity Tab -->
        <div x-show="activeTab === 'activity'" class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="font-semibold">Recent Activity</h2>
                <p class="text-sm text-gray-600">Latest updates from the team</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($project->tasks->sortByDesc('updated_at')->take(10) as $task)
                    @php
                        $statusChanged = $task->updated_at != $task->created_at;
                        $timeAgo = $task->updated_at->diffForHumans();
                    @endphp
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-sm">{{ $task->creator ? substr($task->creator->name, 0, 1) : 'S' }}</div>
                        <div class="flex-1">
                            <p class="text-sm">
                                <span class="font-medium">{{ $task->creator->name ?? 'System' }}</span>
                                @if($statusChanged)
                                    <span> updated task status to {{ $task->status }} on </span>
                                @else
                                    <span> created task </span>
                                @endif
                                <span class="text-[#3f8caf]">"{{ $task->title }}"</span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $timeAgo }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">No recent activity</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
