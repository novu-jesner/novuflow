@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Task Details</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-start justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">{{ $task->title }}</h2>
                <span class="px-3 py-1 text-sm rounded-full
                    @if($task->priority == 'High') bg-orange-100 text-orange-700
                    @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                    @else bg-gray-100 text-gray-700 @endif">{{ $task->priority }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-sm rounded-full
                    @if($task->status == 'To Do') bg-gray-100 text-gray-700
                    @elseif($task->status == 'In Progress') bg-blue-100 text-blue-700
                    @elseif($task->status == 'Review') bg-yellow-100 text-yellow-700
                    @elseif($task->status == 'Completed') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700 @endif">{{ $task->status }}</span>
                <span class="text-sm text-gray-500">in</span>
                <a href="{{ route('projects.show', $task->project_id) }}" class="text-sm text-[#3f8caf] hover:underline">{{ $task->project->name ?? 'Unknown Project' }}</a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Description -->
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $task->description ?? 'No description provided' }}</p>
            </div>

            <!-- Details Grid -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Assigned To</h3>
                    <div class="flex items-center gap-2">
                        @if($task->assignee)
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm">{{ substr($task->assignee->name, 0, 1) }}</div>
                        <span class="text-gray-600">{{ $task->assignee->name }}</span>
                        @else
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white text-sm">-</div>
                        <span class="text-gray-500">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Created By</h3>
                    <div class="flex items-center gap-2">
                        @if($task->creator)
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm">{{ substr($task->creator->name, 0, 1) }}</div>
                        <span class="text-gray-600">{{ $task->creator->name }}</span>
                        @else
                        <span class="text-gray-500">Unknown</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Due Date</h3>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                        </svg>
                        {{ $task->due_date ? $task->due_date->format('F d, Y') : 'No due date' }}
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Created On</h3>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        {{ $task->created_at->format('F d, Y') }}
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            @if($task->members && $task->members->count() > 0)
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Team Members</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($task->members as $member)
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg">
                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">{{ substr($member->name, 0, 1) }}</div>
                        <span class="text-sm text-gray-600">{{ $member->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="p-6 border-t bg-gray-50 flex items-center justify-between">
            <span class="text-sm text-gray-500">Last updated: {{ $task->updated_at->diffForHumans() }}</span>
            <div class="flex gap-3">
                <a href="{{ route('kanban.board', $task->project_id) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-white transition-colors">
                    Back to Board
                </a>
                <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                    Edit Task
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
