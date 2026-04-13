<!-- resources/views/super_admin/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="theme-card">
        <h2 class="text-xl font-semibold">Total Users</h2>
        <p class="text-3xl">{{ $stats['total_users'] ?? 0 }}</p>
    </div>
    <div class="theme-card">
        <h2 class="text-xl font-semibold">Total Projects</h2>
        <p class="text-3xl">{{ $stats['total_projects'] ?? 0 }}</p>
    </div>
    <div class="theme-card">
        <h2 class="text-xl font-semibold">Tasks Status</h2>
        <ul>
            @foreach($stats['tasks_by_status'] ?? [] as $status => $count)
                <li>{{ ucfirst($status) }}: {{ $count }}</li>
            @endforeach
        </ul>
    </div>
</div>

<div class="theme-card">
    <h2 class="text-xl font-semibold mb-2">Tasks Overview</h2>
    <div class="overflow-auto">
        <table class="theme-table w-full border-collapse">
            <thead>
                <tr>
                    <th class="text-left">Task</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Project</th>
                    <th class="text-left">Assigned To</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks ?? [] as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>{{ optional($task->column)->name ?? 'Unassigned' }}</td>
                        <td>{{ $task->project?->name ?? 'No Project' }}</td>
                        <td>{{ optional($task->assigned)->name ?? $task->assigned_to ?? 'Unassigned' }}</td>
                    </tr>
                @empty
                <tr>
                    <td class="text-center" colspan="4">No tasks available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection