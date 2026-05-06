@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'overview', taskTab: 'all', showSettingsModal: false, async updateProject(e) { await submitForm(e.target, { onSuccess: (data) => { window.location.reload(); } }); }, async addMember(e) { await submitForm(e.target, { onSuccess: (data) => { if(data.redirect) window.location.href = data.redirect; } }); } }">
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
                        @if($project->status == 'Active') bg-blue-500
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
            @if(auth()->id() === $project->created_by || in_array(auth()->user()->role, ['SuperAdmin', 'Admin']))
            <button @click="showSettingsModal = true" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.39a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Settings
            </button>
            @endif
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
            <div class="text-2xl font-bold">{{ $project->members->where('pivot.status', 'accepted')->count() + 1 }}</div>
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

        <!-- Activity Tab -->
        <div x-show="activeTab === 'activity'" class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-semibold text-gray-900">Recent Activity</h2>
                        <p class="text-xs text-gray-500">Live updates from the project board</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-100">{{ count($activities) }} Actions</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="relative space-y-8 before:content-[''] before:absolute before:left-[15px] before:top-2 before:bottom-2 before:w-[2px] before:bg-gray-100">
                    @forelse($activities as $activity)
                    <div class="relative pl-10 group">
                        <!-- Activity Icon -->
                        <div class="absolute left-0 top-0 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center text-white text-[10px] font-bold shadow-sm z-10 transition-transform group-hover:scale-110
                            @if($activity['type'] == 'task_created') bg-green-500
                            @elseif($activity['type'] == 'task_updated') bg-blue-500
                            @else bg-purple-500 @endif">
                            {{ $activity['user'] ? substr($activity['user']->name, 0, 1) : 'S' }}
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm text-gray-900">
                                    <span class="font-bold text-gray-900">{{ $activity['user']->name ?? 'System' }}</span>
                                    @if($activity['type'] == 'task_created')
                                        <span class="text-gray-600">created a new task</span>
                                    @elseif($activity['type'] == 'task_updated')
                                        <span class="text-gray-600">moved task to</span>
                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded uppercase tracking-wider">{{ $activity['status'] }}</span>
                                    @else
                                        <span class="text-gray-600">posted a comment on</span>
                                    @endif
                                    
                                    <a href="{{ route('kanban.board', $project->id) }}#task-{{ $activity['task_id'] }}" class="text-[#3f8caf] font-semibold hover:underline decoration-2 underline-offset-2 transition-all">
                                        "{{ $activity['title'] }}"
                                    </a>
                                </div>
                                
                                @if($activity['type'] == 'comment_added' && isset($activity['body']))
                                <div class="mt-2 p-3 bg-gray-50/50 rounded-lg border border-gray-100 text-sm text-gray-600 italic leading-relaxed group-hover:bg-gray-50 transition-colors">
                                    "{{ str($activity['body'])->limit(120) }}"
                                </div>
                                @endif
                            </div>
                            <div class="shrink-0">
                                <span class="text-[11px] font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded group-hover:bg-gray-100 transition-colors">
                                    {{ $activity['date']->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                        </div>
                        <h3 class="text-gray-900 font-medium">No activity yet</h3>
                        <p class="text-sm text-gray-500">Actions taken on the project board will appear here.</p>
                    </div>
                    @endforelse
                </div>
            </div>
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
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Tasks</span>
                            <span class="font-medium">{{ $tasks->count() }}</span>
                        </div>
                        @foreach($statusCounts as $status => $count)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ $status }}</span>
                            <span class="font-medium 
                                @if($status == 'Completed') text-green-600
                                @elseif($status == 'In Progress') text-blue-600
                                @elseif($status == 'Review') text-yellow-600
                                @else text-gray-600 @endif">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" class="space-y-4">
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="font-semibold text-gray-900">Project Tasks</h2>
                            <p class="text-sm text-gray-500">Overview of all tasks in this project</p>
                        </div>
                        <div class="flex gap-1 bg-gray-50 p-1 rounded-lg border border-gray-100 overflow-x-auto scrollbar-hide">
                            <button @click="taskTab = 'all'" class="px-3 py-1.5 text-xs font-medium rounded-md transition-all whitespace-nowrap" :class="taskTab === 'all' ? 'bg-white text-[#3f8caf] shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                All <span class="ml-1 px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded text-[10px]">{{ $tasks->count() }}</span>
                            </button>
                            @foreach($columns as $column)
                            <button @click="taskTab = '{{ $column->name }}'" class="px-3 py-1.5 text-xs font-medium rounded-md transition-all whitespace-nowrap" :class="taskTab === '{{ $column->name }}' ? 'bg-white text-[#3f8caf] shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                                {{ $column->name }} <span class="ml-1 px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded text-[10px]">{{ $statusCounts->get($column->name, 0) }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Tasks List -->
                    <div class="space-y-3">
                        @forelse($tasks as $task)
                        <div x-show="taskTab === 'all' || taskTab === '{{ $task->status }}'" class="group">
                            <a href="{{ route('kanban.board', $project->id) }}#task-{{ $task->id }}" class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:border-[#3f8caf] hover:bg-blue-50/30 transition-all shadow-sm hover:shadow">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="h-10 w-10 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 group-hover:bg-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 group-hover:text-[#3f8caf]">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="text-xs font-bold uppercase tracking-wider
                                                @if($task->priority == 'High') text-red-500
                                                @elseif($task->priority == 'Medium') text-yellow-600
                                                @else text-green-500 @endif">{{ $task->priority }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span class="text-xs font-medium text-gray-500">{{ $task->status }}</span>
                                        </div>
                                        <h4 class="font-medium text-gray-900 group-hover:text-[#3f8caf] transition-colors truncate {{ $task->status == 'Completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</h4>
                                        <div class="flex items-center gap-3 mt-1">
                                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                {{ $task->assignee->name ?? 'Unassigned' }}
                                            </div>
                                            @if($task->due_date)
                                            <div class="flex items-center gap-1.5 text-xs {{ $task->due_date->isPast() && $task->status !== 'Completed' ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" x2="16" y1="2" y2="6"></line><line x1="8" x2="8" y1="2" y2="6"></line><line x1="3" x2="21" y1="10" y2="10"></line></svg>
                                                {{ $task->due_date->format('M d') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 group-hover:text-[#3f8caf] group-hover:translate-x-0.5 transition-all">
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-3">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
                            </svg>
                            <h3 class="text-gray-900 font-medium">No tasks found</h3>
                            <p class="text-sm text-gray-500">There are no tasks associated with this project yet.</p>
                        </div>
                        @endforelse
                    </div>
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
                    <!-- Project Creator (Owner) -->
                    <div class="flex items-center justify-between p-4 border border-blue-100 bg-blue-50/30 rounded-lg shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center text-white font-semibold shadow-sm">{{ substr($project->creator->name, 0, 1) }}</div>
                                <div class="absolute -right-0.5 -bottom-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <div class="font-medium text-gray-900">{{ $project->creator->name }}</div>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold uppercase tracking-wider rounded-full border border-blue-200">Project Owner</span>
                                </div>
                                <div class="text-xs text-gray-500">{{ $project->creator->role }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">{{ $project->tasks->where('created_by', $project->creator->id)->count() }} tasks</div>
                            <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">created</div>
                        </div>
                    </div>

                    <div class="space-y-4">
                    @forelse($project->members as $member)
                        @if($member->id === $project->created_by)
                            @continue
                        @endif
                        @php
                            $completedTasks = $member->assignedTasks->where('project_id', $project->id)->where('status', 'Completed')->count();
                        @endphp
                        <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center text-white font-semibold shadow-sm">{{ substr($member->name, 0, 1) }}</div>
                                    @if($member->pivot->status === 'accepted')
                                        <div class="absolute -right-0.5 -bottom-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                        @if($member->pivot->status === 'pending')
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-[10px] font-bold uppercase tracking-wider rounded-full border border-yellow-200">Invited</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $member->role }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">{{ $completedTasks }} tasks</div>
                                <div class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">completed</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No team members yet</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
    <!-- Settings Modal -->
    <div x-show="showSettingsModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showSettingsModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showSettingsModal" x-transition @click.away="showSettingsModal = false" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold leading-6 text-gray-900" id="modal-title">Project Settings</h3>
                                    <button @click="showSettingsModal = false" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="space-y-8">
                                    <!-- Edit Project Details Section -->
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 mb-4 border-b pb-2">Project Details</h4>
                                        <form method="POST" action="{{ route('projects.update', $project->id) }}" class="space-y-4" @submit.prevent="updateProject">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="team_id" value="{{ $project->team_id }}">
                                            
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="col-span-2 sm:col-span-1">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Project Name</label>
                                                    <input type="text" name="name" value="{{ $project->name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent text-sm">
                                                </div>
                                                <div class="col-span-2 sm:col-span-1">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent text-sm">
                                                        <option value="Active" {{ $project->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                        <option value="On Hold" {{ $project->status == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                                        <option value="Completed" {{ $project->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent text-sm">{{ $project->description }}</textarea>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                                                    <input type="date" name="start_date" value="{{ $project->start_date->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                                                    <input type="date" name="due_date" value="{{ $project->due_date->format('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent text-sm">
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors text-sm">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Member Management Section -->
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 mb-4 border-b pb-2">Project Members</h4>
                                        
                                        <div x-data="{ 
                                            search: '', 
                                            showDropdown: false,
                                            members: {{ $teamMembers->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'email' => $m->email])->values()->toJson() }},
                                            selected: {{ json_encode($currentMemberIds) }},
                                            get filtered() {
                                                return this.members.filter(m => !this.selected.includes(m.id) && (m.name.toLowerCase().includes(this.search.toLowerCase()) || m.email.toLowerCase().includes(this.search.toLowerCase())));
                                            },
                                            get selectedMembers() {
                                                return this.members.filter(m => this.selected.includes(m.id));
                                            }
                                        }">
                                            <form method="POST" action="{{ route('projects.members.sync', $project->id) }}" @submit.prevent="submitForm($event.target, { onSuccess: () => window.location.reload() })">
                                                @csrf
                                                
                                                <!-- Tags (Current & New) -->
                                                <div class="flex flex-wrap gap-2 mb-4 min-h-[40px] p-2 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                                    <template x-for="member in selectedMembers" :key="member.id">
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-sm font-medium border border-blue-200 shadow-sm">
                                                            <span x-text="member.name"></span>
                                                            <button type="button" @click="selected = selected.filter(id => id !== member.id)" class="hover:text-blue-900 transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                                                            </button>
                                                            <input type="hidden" name="member_ids[]" :value="member.id">
                                                        </span>
                                                    </template>
                                                    <div x-show="selected.length === 0" class="text-gray-400 text-sm italic py-1 px-2">No members selected for this project</div>
                                                </div>

                                                <div class="flex gap-2">
                                                    <div class="relative flex-1">
                                                        <input 
                                                            type="text" 
                                                            x-model="search" 
                                                            @focus="showDropdown = true"
                                                            @click.away="showDropdown = false"
                                                            placeholder="Search and add team members..." 
                                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent text-sm"
                                                        >
                                                        
                                                        <div x-show="showDropdown && filtered.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto" style="display: none;">
                                                            <template x-for="member in filtered" :key="member.id">
                                                                <button type="button" @click="selected.push(member.id); search = '';" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex flex-col transition-colors border-b last:border-0 border-gray-100">
                                                                    <span class="text-sm font-medium text-gray-900" x-text="member.name"></span>
                                                                    <span class="text-xs text-gray-500" x-text="member.email"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors text-sm font-medium shadow-sm">
                                                        Update Team
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Danger Zone -->
                                    <div class="pt-4 border-t mt-8">
                                        <h4 class="text-sm font-medium text-red-600 mb-2">Danger Zone</h4>
                                        <p class="text-sm text-gray-500 mb-4">Once you delete a project, there is no going back. All tasks will be permanently removed.</p>
                                        <button type="button" @click="ajaxDelete('{{ route('projects.destroy', $project->id) }}', { onSuccess: (data) => window.location.href = data.redirect })" class="px-4 py-2 border border-red-300 text-red-600 text-sm rounded-md hover:bg-red-50 transition-colors">
                                            Delete Project
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
