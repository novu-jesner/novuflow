<!-- resources/views/super_admin/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6">Dashboard</h1>
{{-- testing--}}
<div class="grid grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold">Total Users</h2>
        <p class="text-3xl">{{ $stats['total_users'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold">Total Projects</h2>
        <p class="text-3xl">{{ $stats['total_projects'] ?? 0 }}</p>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold">Tasks Status</h2>
        <ul>
            @foreach($stats['tasks_by_status'] ?? [] as $status => $count)
                <li>{{ ucfirst($status) }}: {{ $count }}</li>
            @endforeach
        </ul>
    </div>
</div>

<div class="bg-white p-4 rounded shadow">
    <h2 class="text-xl font-semibold mb-2">Tasks Overview</h2>
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2">Task</th>
                <th class="border border-gray-300 p-2">Status</th>
                <th class="border border-gray-300 p-2">Project</th>
                <th class="border border-gray-300 p-2">Assigned To</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks ?? [] as $task)
                <tr>
                	<td class="border border-gray-300 p-2">{{ $task->title }}</td>
                 	<td class="border border-gray-300 p-2">{{ optional($task->column)->name ?? 'Unassigned' }}</td>
                	<td class="border border-gray-300 p-2">{{ $task->project?->name ?? 'No Project' }}</td>
                	<td class="border border-gray-300 p-2">{{ optional($task->assigned)->name ?? $task->assigned_to ?? 'Unassigned' }}</td>
                </tr>
            @empty
            <tr>
                <td class="border border-gray-300 p-2 text-center" colspan="4">No tasks available</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection