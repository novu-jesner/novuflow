@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Search Header -->
    <div>
        <h1 class="text-3xl font-semibold text-foreground">
            Search Results
        </h1>
        <p class="text-muted-foreground mt-1">
            @if($query)
                Results for "{{ $query }}"
            @else
                Enter a search term to find tasks and projects
            @endif
        </p>
    </div>

    @if($query)
        <!-- Tasks Section -->
        @if($tasks->count() > 0)
        <div class="bg-card border border-border rounded-lg shadow">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="text-lg font-semibold">Tasks ({{ $tasks->count() }})</h2>
            </div>
            <div class="divide-y divide-border">
                @foreach($tasks as $task)
                <div class="px-6 py-4 hover:bg-muted/30 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium">
                                <a href="{{ route('tasks.show', $task->id) }}" class="text-primary hover:underline">
                                    {{ $task->title }}
                                </a>
                            </h3>
                            @if($task->description)
                            <p class="text-sm text-muted-foreground mt-1 line-clamp-2">
                                {{ Str::limit($task->description, 150) }}
                            </p>
                            @endif
                            <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                <span>Project: {{ $task->project->name ?? 'N/A' }}</span>
                                <span>Status: {{ ucfirst($task->status) }}</span>
                                @if($task->priority)
                                <span>Priority: {{ ucfirst($task->priority) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Projects Section -->
        @if($projects->count() > 0)
        <div class="bg-card border border-border rounded-lg shadow">
            <div class="px-6 py-4 border-b border-border">
                <h2 class="text-lg font-semibold">Projects ({{ $projects->count() }})</h2>
            </div>
            <div class="divide-y divide-border">
                @foreach($projects as $project)
                <div class="px-6 py-4 hover:bg-muted/30 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="font-medium">
                                <a href="{{ route('projects.show', $project->id) }}" class="text-primary hover:underline">
                                    {{ $project->name }}
                                </a>
                            </h3>
                            @if($project->description)
                            <p class="text-sm text-muted-foreground mt-1 line-clamp-2">
                                {{ Str::limit($project->description, 150) }}
                            </p>
                            @endif
                            <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                <span>Team: {{ $project->team->name ?? 'N/A' }}</span>
                                <span>Status: {{ ucfirst($project->status) }}</span>
                                <span>Progress: {{ $project->progress }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($tasks->count() == 0 && $projects->count() == 0)
        <div class="bg-card border border-border rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium">No results found</h3>
            <p class="mt-2 text-muted-foreground">
                Try adjusting your search terms or check for typos.
            </p>
        </div>
        @endif
    @else
        <div class="bg-card border border-border rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium">Search for tasks and projects</h3>
            <p class="mt-2 text-muted-foreground">
                Enter keywords in the search box above to find relevant tasks and projects.
            </p>
        </div>
    @endif
</div>
@endsection