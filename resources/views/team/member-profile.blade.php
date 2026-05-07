@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    <div>
        <a href="{{ route('team.index') }}" class="text-primary hover:opacity-90 flex items-center gap-1 mb-4 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"></path>
                <path d="M19 12H5"></path>
            </svg>
            Back to Team
        </a>
        <h1 class="text-3xl font-semibold text-foreground">{{ $user->name }}</h1>
        <p class="text-muted-foreground mt-1">{{ $user->email }}</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h2 class="font-semibold mb-4">Profile Information</h2>
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-muted-foreground">Role</div>
                    <div class="font-medium">{{ $user->role }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted-foreground">Teams</div>
                    <div class="font-medium">{{ $user->teams->pluck('name')->join(', ') ?: 'No teams assigned' }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted-foreground">Joined</div>
                    <div class="font-medium">{{ $user->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h2 class="font-semibold mb-4">Task Statistics</h2>
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-muted-foreground">Total Tasks</div>
                    <div class="font-medium">{{ $user->tasks->count() }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted-foreground">Completed</div>
                    <div class="font-medium text-green-600">{{ $user->tasks->where('status', 'Completed')->count() }}</div>
                </div>
                <div>
                    <div class="text-sm text-muted-foreground">In Progress</div>
                    <div class="font-medium text-blue-600">{{ $user->tasks->whereIn('status', ['To Do', 'In Progress', 'Review'])->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
