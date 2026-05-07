@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('projects.index') }}" class="text-primary hover:opacity-90 flex items-center gap-2 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"></path>
            </svg>
            Back to Projects
        </a>
    </div>

    <div class="bg-card border border-border rounded-lg shadow overflow-hidden">
    <div class="p-6 border-b border-border">
        <h1 class="text-2xl font-semibold">Create New Project</h1>
        <p class="text-muted-foreground mt-1">Fill in the details to create a new project</p>
    </div>

    @if(session('error'))
        <div class="mx-6 mt-4 p-3 bg-destructive/15 text-destructive border border-border rounded-md">
            {{ session('error') }}
        </div>
    @endif
        <form action="{{ route('projects.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="space-y-2">
                <label for="name" class="text-sm font-medium">Project Name</label>
                <input type="text" id="name" name="name" required class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors">
            </div>
            <div class="space-y-2">
                <label for="description" class="text-sm font-medium">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium">Status</label>
                    <select id="status" name="status" required class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors">
                        <option value="Active">Active</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label for="team_id" class="text-sm font-medium">Team</label>
                    <select id="team_id" name="team_id" class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors">
                        <option value="">No Team</option>
                        @foreach(\App\Models\Team::all() as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="start_date" class="text-sm font-medium">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors">
                </div>
                <div class="space-y-2">
                    <label for="due_date" class="text-sm font-medium">Due Date</label>
                    <input type="date" id="due_date" name="due_date" required class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('projects.index') }}" class="px-4 py-2 border border-border rounded-md hover:bg-muted/30 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-primary-foreground rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">Create Project</button>
            </div>
        </form>
    </div>
</div>

@endsection
