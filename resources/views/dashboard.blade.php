@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @foreach($stats as $key => $value)
            <div class="theme-card">
                <h2 class="text-sm font-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}</h2>
                <p class="text-3xl font-bold">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="theme-card">
        <h2 class="text-xl font-semibold mb-2">Tasks Overview</h2>
        <div class="overflow-auto">
            <table class="theme-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">Task</th>
                        <th class="text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">Status</th>
                        <th class="text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">Project</th>
                        <th class="text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">Assigned To</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td class="text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">{{ $task->title }}</td>
                        <td class="text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">{{ optional($task->column)->name ?? 'Unassigned' }}</td>
                        <td class="text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">{{ $task->project?->name }}</td>
                        <td class="text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700 p-2">{{ optional($task->assigned)->name ?? $task->assigned_to ?? 'Unassigned' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection