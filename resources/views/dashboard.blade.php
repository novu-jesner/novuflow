<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css'])
</head>
<body class="p-6 bg-gray-100">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold">Total Users</h2>
            <p class="text-3xl">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold">Total Projects</h2>
            <p class="text-3xl">{{ $totalProjects }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold">Tasks Status</h2>
            <ul>
                @foreach($tasksByStatus as $status => $count)
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
                @foreach($tasks as $task)
                <tr>
                    <td class="border border-gray-300 p-2">{{ $task->title }}</td>
                    <td class="border border-gray-300 p-2">{{ optional($task->column)->name ?? 'Unassigned' }}</td>
                    <td class="border border-gray-300 p-2">{{ $task->project?->name }}</td>
                    <td class="border border-gray-300 p-2">{{ optional($task->assigned)->name ?? $task->assigned_to ?? 'Unassigned' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($stats as $key => $value)
            <div class="bg-white shadow rounded p-4">
                <h2 class="text-gray-600 font-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}</h2>
                <p class="text-3xl font-bold">{{ $value }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection