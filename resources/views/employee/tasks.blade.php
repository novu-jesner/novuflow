@extends('layouts.dashboard')

@section('dashboard-content')
@php
    $user = auth()->user();
    $isEmployee = $user->isEmployee();
    $isTeamLeader = $user->isTeamLeader();
    $isAdmin = $user->isAdmin();
    $showFilters = !$isEmployee;
    $showTeamFilter = $isAdmin;
    $filterCount = collect(request()->only(['search', 'status', 'priority', 'assignee', 'project_id', 'team_id', 'due_date_start', 'due_date_end']))
        ->filter(fn($value) => filled($value) && $value !== 'all')->count();
@endphp

<div class="space-y-6" x-data="{ activeTab: '{{ request('status_tab', 'all') }}', showFilterModal: false }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-foreground">
            @if($isEmployee) My Tasks
            @elseif($isTeamLeader) Team Tasks
            @else All Tasks
            @endif
        </h1>
        <p class="text-muted-foreground mt-1">
            @if($isEmployee) View and manage all your assigned tasks.
            @elseif($isTeamLeader) View and manage all tasks within your team.
            @else View and manage every task across users, teams, and projects.
            @endif
        </p>
    </div>

    @if($showFilters)
    <!-- Filter Bar -->
    <div class="bg-card border border-border rounded-xl p-4 sm:p-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex-1 min-w-0 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3">
            <input id="search" name="search" type="search" form="taskFilters" value="{{ request('search') }}" placeholder="Search tasks..." class="min-w-0 flex-1 rounded-md border border-input bg-background px-4 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
            <button type="submit" form="taskFilters" class="inline-flex items-center rounded-md bg-primary px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition-colors">Search</button>
            <button type="button" @click="showFilterModal = true" class="inline-flex items-center rounded-md border border-border bg-background px-4 py-3 text-sm font-semibold text-foreground hover:bg-muted/30 transition-colors">
                Filters
                @if($filterCount > 0)
                <span class="ml-2 inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-primary text-xs font-semibold text-white">{{ $filterCount }}</span>
                @endif
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <label for="sort" class="sr-only">Sort</label>
            <select id="sort" name="sort" form="taskFilters" class="rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                <option value="created_at" @selected(request('sort') === 'created_at')>Date Created</option>
                <option value="title" @selected(request('sort') === 'title')>Title</option>
                <option value="priority" @selected(request('sort') === 'priority')>Priority</option>
                <option value="due_date" @selected(request('sort') === 'due_date')>Due Date</option>
                <option value="assignee" @selected(request('sort') === 'assignee')>Assignee</option>
                <option value="project" @selected(request('sort') === 'project')>Project</option>
                @if($showTeamFilter)
                <option value="team" @selected(request('sort') === 'team')>Team</option>
                @endif
            </select>
            <label for="direction" class="sr-only">Direction</label>
            <select id="direction" name="direction" form="taskFilters" class="rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                <option value="desc" @selected(request('direction') === 'desc')>Newest First</option>
                <option value="asc" @selected(request('direction') === 'asc')>Oldest First</option>
            </select>
        </div>
    </div>
    @endif

    @if($showFilters)
    <!-- Filter Modal -->
    <div x-show="showFilterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showFilterModal = false"></div>
        <div class="relative z-10 w-full max-w-4xl overflow-hidden rounded-3xl border border-border bg-card shadow-xl">
            <form id="taskFilters" action="{{ route('employee.tasks') }}" method="GET" class="space-y-6 p-6">
                <input type="hidden" name="status_tab" x-model="activeTab">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-foreground">Advanced Filters</h2>
                        <p class="text-sm text-muted-foreground">Filter tasks by status, project, team, assignee, priority, and due date.</p>
                    </div>
                    <button type="button" @click="showFilterModal = false" class="rounded-full border border-border bg-background p-2 text-sm text-foreground hover:bg-muted/40 transition-colors">Close</button>
                </div>

                <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="modal_status" class="block text-sm font-medium text-foreground mb-2">Status</label>
                        <select id="modal_status" name="status" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="all">All Statuses</option>
                            @foreach(['To Do', 'In Progress', 'Review', 'Completed'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected(request('status') === $statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="modal_priority" class="block text-sm font-medium text-foreground mb-2">Priority</label>
                        <select id="modal_priority" name="priority" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="all">All Priorities</option>
                            <option value="High" @selected(request('priority') === 'High')>High</option>
                            <option value="Medium" @selected(request('priority') === 'Medium')>Medium</option>
                            <option value="Low" @selected(request('priority') === 'Low')>Low</option>
                        </select>
                    </div>
                    <div>
                        <label for="modal_project_id" class="block text-sm font-medium text-foreground mb-2">Project</label>
                        <select id="modal_project_id" name="project_id" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="all">All Projects</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($showTeamFilter)
                    <div>
                        <label for="modal_team_id" class="block text-sm font-medium text-foreground mb-2">Team</label>
                        <select id="modal_team_id" name="team_id" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="all">All Teams</option>
                            @foreach($teams as $team)
                            <option value="{{ $team->id }}" @selected(request('team_id') == $team->id)>{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div>
                        <label for="modal_assignee" class="block text-sm font-medium text-foreground mb-2">Assignee</label>
                        <select id="modal_assignee" name="assignee" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                            <option value="all">All Assignees</option>
                            <option value="unassigned" @selected(request('assignee') === 'unassigned')>Unassigned</option>
                            @foreach($assignees as $assignee)
                            <option value="{{ $assignee->id }}" @selected(request('assignee') == $assignee->id)>{{ $assignee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="modal_due_date_start" class="block text-sm font-medium text-foreground mb-2">Due Date Start</label>
                            <input id="modal_due_date_start" name="due_date_start" type="date" value="{{ request('due_date_start') }}" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label for="modal_due_date_end" class="block text-sm font-medium text-foreground mb-2">Due Date End</label>
                            <input id="modal_due_date_end" name="due_date_end" type="date" value="{{ request('due_date_end') }}" class="w-full rounded-md border border-input bg-background px-3 py-3 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-3 border-t border-border">
                    <a href="{{ route('employee.tasks') }}" class="inline-flex items-center justify-center rounded-md border border-border bg-background px-4 py-3 text-sm font-semibold text-foreground hover:bg-muted/40 transition-colors">Clear Filters</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-3 text-sm font-semibold text-white hover:bg-primary/90 transition-colors">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="bg-card border border-border rounded-xl p-4">
            <div class="text-sm text-muted-foreground">Total Tasks</div>
            <div class="text-2xl font-bold text-foreground">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-card border border-border rounded-xl p-4">
            <div class="text-sm text-muted-foreground">Overdue</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
        </div>
        <div class="bg-card border border-border rounded-xl p-4">
            <div class="text-sm text-muted-foreground">Due Today</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['due_today'] }}</div>
        </div>
        <div class="bg-card border border-border rounded-xl p-4">
            <div class="text-sm text-muted-foreground">High Priority</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['high_priority'] }}</div>
        </div>
    </div>

    <!-- Tasks Tabs -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b border-border overflow-x-auto scrollbar-hide">
            <button @click="activeTab = 'all'" 
                class="px-4 py-3 text-sm font-medium border-b-2 transition-all whitespace-nowrap flex items-center gap-2" 
                :class="activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">
                All Tasks
                <span class="px-1.5 py-0.5 text-[10px] rounded-full" :class="activeTab === 'all' ? 'bg-primary text-white' : 'bg-muted/40 text-muted-foreground border border-border'">{{ $tasks->count() }}</span>
            </button>
            @foreach($statuses as $status)
            @php $slug = Str::slug($status); @endphp
            <button @click="activeTab = '{{ $slug }}'" 
                class="px-4 py-3 text-sm font-medium border-b-2 transition-all whitespace-nowrap flex items-center gap-2" 
                :class="activeTab === '{{ $slug }}' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">
                {{ $status }}
                <span class="px-1.5 py-0.5 text-[10px] rounded-full" :class="activeTab === '{{ $slug }}' ? 'bg-primary text-white' : 'bg-muted/40 text-muted-foreground border border-border'">{{ $groupedTasks->get($status, collect())->count() }}</span>
            </button>
            @endforeach
        </div>

        <div x-show="activeTab === 'all'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($tasks as $task)
                @include('employee.partials.task-card', ['task' => $task])
            @empty
                <div class="col-span-full py-12 text-center bg-card rounded-xl border border-dashed border-border">
                    <p class="text-muted-foreground">No tasks match your filters.</p>
                </div>
            @endforelse
        </div>

        @foreach($statuses as $status)
        @php $slug = Str::slug($status); @endphp
        <div x-show="activeTab === '{{ $slug }}'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" style="display: none;">
            @forelse($groupedTasks->get($status, collect()) as $task)
                @include('employee.partials.task-card', ['task' => $task])
            @empty
                <div class="col-span-full py-12 text-center bg-card rounded-xl border border-dashed border-border">
                    <p class="text-muted-foreground">No tasks in {{ $status }}.</p>
                </div>
            @endforelse
        </div>
        @endforeach
    </div>
</div>
@endsection
