@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ 
        showTaskModal: false,
        draggedTask: null,
        draggedEl: null,
        async updateTaskStatus(taskId, newStatus, colId) {
            const card = document.getElementById('task-' + taskId);
            const targetCol = document.getElementById(colId).querySelector('.task-list');
            
            if (card && targetCol) {
                targetCol.appendChild(card);
                this.updateCounters();
            }

            try {
                const response = await fetch('/dashboard/tasks/' + taskId + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                if (response.ok) {
                    $store.toast.show('Task moved to ' + newStatus, 'success');
                } else {
                    $store.toast.show('Failed to update task status', 'error');
                    window.location.reload();
                }
            } catch (error) {
                $store.toast.show('Network error', 'error');
                window.location.reload();
            }
        },
        updateCounters() {
            document.querySelectorAll('.kanban-column').forEach(col => {
                const count = col.querySelectorAll('.task-card').length;
                col.querySelector('.task-count').textContent = count;
                const noTasksMsg = col.querySelector('.no-tasks');
                if (noTasksMsg) {
                    noTasksMsg.style.display = count === 0 ? 'block' : 'none';
                }
            });
        },
        async createTask(e) {
            const form = e.target;
            await submitForm(form, {
                resetForm: true,
                onSuccess: (data) => {
                    this.showTaskModal = false;
                    window.location.reload();
                }
            });
        }
    }">


    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">{{ $project->name }}</h1>
            <p class="text-gray-600 mt-1">Project board for {{ $project->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('projects.show', $project->id) }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l-.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.39a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Project Details
            </a>
            <button @click="showTaskModal = true" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Add Task
            </button>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div x-show="showTaskModal" x-cloak class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showTaskModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl w-full max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4">Add New Task</h3>
                    <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST" class="space-y-4" @submit.prevent="createTask($event)">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Task Title</label>
                            <input type="text" id="title" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                        </div>
                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="To Do">To Do</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Review">Review</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                <select id="priority" name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" id="due_date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                            </div>
                            <div class="space-y-2">
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                                <select id="assigned_to" name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="">Unassigned</option>
                                    @foreach($project->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="submit" form="addTaskForm" class="inline-flex justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">Add Task</button>
                    <button type="button" @click="showTaskModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 overflow-x-auto">
        <!-- To Do Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px] kanban-column" id="col-to-do"
            @dragover.prevent
            @drop.prevent="if (draggedTask) { updateTaskStatus(draggedTask, 'To Do', 'col-to-do'); draggedTask = null; }"
            :class="{ 'bg-blue-50 ring-2 ring-blue-300': draggedTask }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">To Do</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full task-count">{{ $todoTasks->count() }}</span>
            </div>
            <div class="space-y-3 min-h-[100px] task-list">
                @foreach($todoTasks as $task)
                <a href="{{ route('tasks.show', $task->id) }}" id="task-{{ $task->id }}" class="block bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow group task-card"
                    draggable="true"
                    @dragstart="draggedTask = {{ $task->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900 group-hover:text-[#3f8caf] transition-colors">{{ $task->title }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700 @endif">{{ $task->priority }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                    <div class="flex items-center justify-between">
                        @if($task->assignee)
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ substr($task->assignee->name, 0, 1) }}</div>
                        @else
                        <div class="w-6 h-6 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-white text-xs">-</div>
                        @endif
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <span>{{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
                <div class="text-center py-8 text-gray-400 text-sm no-tasks" style="display: {{ $todoTasks->count() === 0 ? 'block' : 'none' }}">No tasks</div>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px] kanban-column" id="col-in-progress"
            @dragover.prevent
            @drop.prevent="if (draggedTask) { updateTaskStatus(draggedTask, 'In Progress', 'col-in-progress'); draggedTask = null; }"
            :class="{ 'bg-blue-50 ring-2 ring-blue-300': draggedTask }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">In Progress</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full task-count">{{ $inProgressTasks->count() }}</span>
            </div>
            <div class="space-y-3 min-h-[100px] task-list">
                @foreach($inProgressTasks as $task)
                <a href="{{ route('tasks.show', $task->id) }}" id="task-{{ $task->id }}" class="block bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow group task-card"
                    draggable="true"
                    @dragstart="draggedTask = {{ $task->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900 group-hover:text-[#3f8caf] transition-colors">{{ $task->title }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700 @endif">{{ $task->priority }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                    <div class="flex items-center justify-between">
                        @if($task->assignee)
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ substr($task->assignee->name, 0, 1) }}</div>
                        @else
                        <div class="w-6 h-6 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-white text-xs">-</div>
                        @endif
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <span>{{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
                <div class="text-center py-8 text-gray-400 text-sm no-tasks" style="display: {{ $inProgressTasks->count() === 0 ? 'block' : 'none' }}">No tasks</div>
            </div>
        </div>

        <!-- Review Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px] kanban-column" id="col-review"
            @dragover.prevent
            @drop.prevent="if (draggedTask) { updateTaskStatus(draggedTask, 'Review', 'col-review'); draggedTask = null; }"
            :class="{ 'bg-blue-50 ring-2 ring-blue-300': draggedTask }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">Review</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full task-count">{{ $reviewTasks->count() }}</span>
            </div>
            <div class="space-y-3 min-h-[100px] task-list">
                @foreach($reviewTasks as $task)
                <a href="{{ route('tasks.show', $task->id) }}" id="task-{{ $task->id }}" class="block bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow group task-card"
                    draggable="true"
                    @dragstart="draggedTask = {{ $task->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900 group-hover:text-[#3f8caf] transition-colors">{{ $task->title }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($task->priority == 'High') bg-orange-100 text-orange-700
                            @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                            @else bg-gray-100 text-gray-700 @endif">{{ $task->priority }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                    <div class="flex items-center justify-between">
                        @if($task->assignee)
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ substr($task->assignee->name, 0, 1) }}</div>
                        @else
                        <div class="w-6 h-6 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-white text-xs">-</div>
                        @endif
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <span>{{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
                <div class="text-center py-8 text-gray-400 text-sm no-tasks" style="display: {{ $reviewTasks->count() === 0 ? 'block' : 'none' }}">No tasks</div>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px] kanban-column" id="col-completed"
            @dragover.prevent
            @drop.prevent="if (draggedTask) { updateTaskStatus(draggedTask, 'Completed', 'col-completed'); draggedTask = null; }"
            :class="{ 'bg-blue-50 ring-2 ring-blue-300': draggedTask }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">Completed</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full task-count">{{ $completedTasks->count() }}</span>
            </div>
            <div class="space-y-3 min-h-[100px] task-list">
                @foreach($completedTasks as $task)
                <a href="{{ route('tasks.show', $task->id) }}" id="task-{{ $task->id }}" class="block bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow opacity-75 group task-card"
                    draggable="true"
                    @dragstart="draggedTask = {{ $task->id }}">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900 line-through group-hover:text-[#3f8caf] transition-colors">{{ $task->title }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Done</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $task->description }}</p>
                    <div class="flex items-center justify-between">
                        @if($task->assignee)
                        <div class="w-6 h-6 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">{{ substr($task->assignee->name, 0, 1) }}</div>
                        @else
                        <div class="w-6 h-6 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-white text-xs">-</div>
                        @endif
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <span>{{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
                <div class="text-center py-8 text-gray-400 text-sm no-tasks" style="display: {{ $completedTasks->count() === 0 ? 'block' : 'none' }}">No tasks</div>
            </div>
        </div>
    </div>
</div>
@endsection
