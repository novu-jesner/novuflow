@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'all' }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-gray-900">My Tasks</h1>
        <p class="text-gray-600 mt-1">View and manage all your assigned tasks</p>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                To Do
            </h3>
            <div class="text-2xl font-bold">{{ $todoTasks->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" x2="12" y1="8" y2="12"></line>
                    <line x1="12" x2="12.01" y1="16" y2="16"></line>
                </svg>
                In Progress
            </h3>
            <div class="text-2xl font-bold">{{ $inProgressTasks->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" x2="12" y1="8" y2="12"></line>
                    <line x1="12" x2="12.01" y1="16" y2="16"></line>
                </svg>
                In Review
            </h3>
            <div class="text-2xl font-bold">{{ $reviewTasks->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Completed
            </h3>
            <div class="text-2xl font-bold">{{ $completedTasks->count() }}</div>
        </div>
    </div>

    <!-- Tasks Tabs -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b">
            <button @click="activeTab = 'all'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'all' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">All Tasks</button>
            <button @click="activeTab = 'todo'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'todo' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">To Do</button>
            <button @click="activeTab = 'inprogress'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'inprogress' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">In Progress</button>
            <button @click="activeTab = 'review'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'review' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Review</button>
            <button @click="activeTab = 'completed'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'completed' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Completed</button>
        </div>

        <!-- All Tasks Tab -->
        <div x-show="activeTab === 'all'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($tasks as $task)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $task->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">{{ $task->priority }}</span>
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $task->status }}</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ $task->project->name ?? 'No Project' }}</span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ $task->creator ? substr($task->creator->name, 0, 1) : 'S' }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-500">No tasks assigned to you</div>
            @endforelse
        </div>

        <!-- To Do Tab -->
        <div x-show="activeTab === 'todo'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" style="display: none;">
            @forelse($todoTasks as $task)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $task->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">{{ $task->priority }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $task->status }}</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ $task->project->name ?? 'No Project' }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ $task->creator ? substr($task->creator->name, 0, 1) : 'S' }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-500">No tasks in To Do</div>
            @endforelse
        </div>

        <!-- In Progress Tab -->
        <div x-show="activeTab === 'inprogress'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" style="display: none;">
            @forelse($inProgressTasks as $task)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $task->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">{{ $task->priority }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ $task->status }}</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ $task->project->name ?? 'No Project' }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ $task->creator ? substr($task->creator->name, 0, 1) : 'S' }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-500">No tasks in progress</div>
            @endforelse
        </div>

        <!-- Review Tab -->
        <div x-show="activeTab === 'review'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" style="display: none;">
            @forelse($reviewTasks as $task)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $task->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-green-100 text-green-700 @endif">{{ $task->priority }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded">{{ $task->status }}</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ $task->project->name ?? 'No Project' }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ $task->creator ? substr($task->creator->name, 0, 1) : 'S' }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-500">No tasks in review</div>
            @endforelse
        </div>

        <!-- Completed Tab -->
        <div x-show="activeTab === 'completed'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" style="display: none;">
            @forelse($completedTasks as $task)
            <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow opacity-75">
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900 line-through">{{ $task->title }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $task->description }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Done</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">{{ $task->status }}</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">{{ $task->project->name ?? 'No Project' }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                    <line x1="16" x2="16" y1="2" y2="6"></line>
                                    <line x1="8" x2="8" y1="2" y2="6"></line>
                                    <line x1="3" x2="21" y1="10" y2="10"></line>
                                </svg>
                                <span>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</span>
                            </div>
                        </div>
                        <div class="w-6 h-6 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ $task->creator ? substr($task->creator->name, 0, 1) : 'S' }}</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-gray-500">No completed tasks</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
