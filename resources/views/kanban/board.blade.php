@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ 
        showTaskModal: false,
        showEditModal: false,
        showColumnModal: false,
        showEditColumnModal: false,
        showDeleteColumnModal: false,
        isSubmitting: false,
        editingTask: {
            id: null,
            title: '',
            description: '',
            status: '',
            priority: '',
            due_date: '',
            assigned_to: ''
        },
        editingColumn: {
            id: null,
            name: ''
        },
        draggedTask: null,
        draggedColumn: null,
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
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = e.target;
            try {
                await submitForm(form, {
                    resetForm: true,
                    onSuccess: (data) => {
                        this.showTaskModal = false;
                        window.location.reload();
                    },
                    onError: () => {
                        this.isSubmitting = false;
                    }
                });
            } catch (err) {
                this.isSubmitting = false;
            }
        },
        async updateTask(e) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = e.target;
            try {
                await submitForm(form, {
                    onSuccess: (data) => {
                        this.showEditModal = false;
                        window.location.reload();
                    },
                    onError: () => {
                        this.isSubmitting = false;
                    }
                });
            } catch (err) {
                this.isSubmitting = false;
            }
        },
        async addColumn(e) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = e.target;
            try {
                await submitForm(form, {
                    resetForm: true,
                    onSuccess: (data) => {
                        this.showColumnModal = false;
                        window.location.reload();
                    },
                    onError: () => {
                        this.isSubmitting = false;
                    }
                });
            } catch (err) {
                this.isSubmitting = false;
            }
        },
        async updateColumn(e) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = e.target;
            try {
                await submitForm(form, {
                    onSuccess: (data) => {
                        this.showEditColumnModal = false;
                        window.location.reload();
                    },
                    onError: () => {
                        this.isSubmitting = false;
                    }
                });
            } catch (err) {
                this.isSubmitting = false;
            }
        },
        async deleteColumn(e) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = e.target;
            try {
                await submitForm(form, {
                    onSuccess: (data) => {
                        this.showDeleteColumnModal = false;
                        window.location.reload();
                    },
                    onError: () => {
                        this.isSubmitting = false;
                    }
                });
            } catch (err) {
                this.isSubmitting = false;
            }
        },
        async reorderColumns() {
            const columnIds = Array.from(document.querySelectorAll('.kanban-column')).map(el => el.id.replace('col-', ''));
            try {
                const response = await fetch('{{ route('projects.columns.reorder', $project->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ orders: columnIds })
                });
                if (response.ok) {
                    $store.toast.show('Columns reordered', 'success');
                }
            } catch (error) {
                console.error(error);
            }
        },
        openEditModal(task) {
            this.editingTask = {
                id: task.id,
                title: task.title,
                description: task.description || '',
                status: task.status,
                priority: task.priority,
                due_date: task.due_date ? task.due_date.substring(0, 10) : '',
                assigned_to: task.assigned_to || ''
            };
            this.showEditModal = true;
        },
        openEditColumnModal(column) {
            this.editingColumn = {
                id: column.id,
                name: column.name
            };
            this.showEditColumnModal = true;
        },
        openDeleteColumnModal(column) {
            this.editingColumn = {
                id: column.id,
                name: column.name
            };
            this.showDeleteColumnModal = true;
        },
        handleColumnDrop(e, targetColId) {
            if (!this.draggedColumn) return;
            const draggedCol = document.getElementById('col-' + this.draggedColumn);
            const targetCol = document.getElementById(targetColId);
            if (draggedCol && targetCol && draggedCol !== targetCol) {
                const board = targetCol.parentNode;
                const children = Array.from(board.children);
                const draggedIndex = children.indexOf(draggedCol);
                const targetIndex = children.indexOf(targetCol);
                
                if (draggedIndex < targetIndex) {
                    targetCol.after(draggedCol);
                } else {
                    targetCol.before(draggedCol);
                }
                this.reorderColumns();
            }
            this.draggedColumn = null;
        }
    }">

    @php
        $isCreator = auth()->id() === $project->created_by || auth()->user()->role === 'SuperAdmin' || auth()->user()->role === 'Admin';
    @endphp

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
            @if($isCreator)
            <button @click="showColumnModal = true" class="px-4 py-2 border border-[#3f8caf] text-[#3f8caf] rounded-md hover:bg-blue-50 transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Add Column
            </button>
            @endif
            @if(auth()->user()->role !== 'Employee')
            <button @click="showTaskModal = true" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Add Task
            </button>
            @endif
        </div>
    </div>

    <!-- Add Column Modal -->
    <div x-show="showColumnModal" x-cloak class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showColumnModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl w-full max-w-sm">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4">Add New Column</h3>
                    <form id="addColumnForm" action="{{ route('projects.columns.add', $project->id) }}" method="POST" class="space-y-4" @submit.prevent="addColumn($event)">
                        @csrf
                        <div class="space-y-2">
                            <label for="col_name" class="block text-sm font-medium text-gray-700">Column Name</label>
                            <input type="text" id="col_name" name="name" required placeholder="e.g. Backlog, Testing" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="submit" form="addColumnForm" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="inline-flex justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                        <span x-show="!isSubmitting">Add Column</span>
                        <span x-show="isSubmitting">Adding...</span>
                    </button>
                    <button type="button" @click="showColumnModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Column Modal -->
    <div x-show="showEditColumnModal" x-cloak class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditColumnModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl w-full max-w-sm">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4">Edit Column</h3>
                    <form id="editColumnForm" :action="'/dashboard/projects/{{ $project->id }}/columns/' + editingColumn.id" method="POST" class="space-y-4" @submit.prevent="updateColumn($event)">
                        @csrf
                        @method('PUT')
                        <div class="space-y-2">
                            <label for="edit_col_name" class="block text-sm font-medium text-gray-700">Column Name</label>
                            <input type="text" id="edit_col_name" name="name" x-model="editingColumn.name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="submit" form="editColumnForm" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="inline-flex justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                        <span x-show="!isSubmitting">Update Column</span>
                        <span x-show="isSubmitting">Updating...</span>
                    </button>
                    <button type="button" @click="showEditColumnModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Column Modal -->
    <div x-show="showDeleteColumnModal" x-cloak class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteColumnModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl w-full max-w-sm">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-semibold leading-6 text-red-600 mb-4">Delete Column</h3>
                    <p class="text-sm text-gray-600 mb-4">Are you sure you want to delete the column <span class="font-bold text-gray-900" x-text="editingColumn.name"></span>? All tasks in this column will be moved to the first available column.</p>
                    <form id="deleteColumnForm" :action="'/dashboard/projects/{{ $project->id }}/columns/' + editingColumn.id" method="POST" @submit.prevent="deleteColumn($event)">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="submit" form="deleteColumnForm" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="inline-flex justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                        <span x-show="!isSubmitting">Delete Column</span>
                        <span x-show="isSubmitting">Deleting...</span>
                    </button>
                    <button type="button" @click="showDeleteColumnModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
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
                                    @foreach($columns as $column)
                                    <option value="{{ $column->name }}">{{ $column->name }}</option>
                                    @endforeach
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
                    <button type="submit" form="addTaskForm" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="inline-flex justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                        <span x-show="!isSubmitting">Add Task</span>
                        <span x-show="isSubmitting">Adding...</span>
                    </button>
                    <button type="button" @click="showTaskModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    @if(auth()->user()->role !== 'Employee')
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl w-full max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4">Edit Task</h3>
                    <form id="editTaskForm" :action="'/dashboard/tasks/' + editingTask.id" method="POST" class="space-y-4" @submit.prevent="updateTask($event)">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <div class="space-y-2">
                            <label for="edit_title" class="block text-sm font-medium text-gray-700">Task Title</label>
                            <input type="text" id="edit_title" name="title" x-model="editingTask.title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                        </div>
                        <div class="space-y-2">
                            <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="edit_description" name="description" x-model="editingTask.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="edit_status" name="status" x-model="editingTask.status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    @foreach($columns as $column)
                                    <option value="{{ $column->name }}">{{ $column->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="edit_priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                <select id="edit_priority" name="priority" x-model="editingTask.priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="edit_due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" id="edit_due_date" name="due_date" x-model="editingTask.due_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                            </div>
                            <div class="space-y-2">
                                <label for="edit_assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                                <select id="edit_assigned_to" name="assigned_to" x-model="editingTask.assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="">Unassigned</option>
                                    @foreach($projectMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="submit" form="editTaskForm" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="inline-flex justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                        <span x-show="!isSubmitting">Update Task</span>
                        <span x-show="isSubmitting">Updating...</span>
                    </button>
                    <button type="button" @click="showEditModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Kanban Board -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 overflow-x-auto pb-4">
        @foreach($columns as $column)
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px] kanban-column group/col" id="col-{{ $column->id }}"
            draggable="{{ $isCreator ? 'true' : 'false' }}"
            @dragstart="if ({{ $isCreator ? 'true' : 'false' }} && !draggedTask) { draggedColumn = {{ $column->id }} }"
            @dragover.prevent
            @drop.prevent="if (draggedTask) { updateTaskStatus(draggedTask, '{{ $column->name }}', 'col-{{ $column->id }}'); draggedTask = null; } else { handleColumnDrop($event, 'col-{{ $column->id }}') }"
            :class="{ 'bg-blue-50 ring-2 ring-blue-300': draggedTask || (draggedColumn && draggedColumn == {{ $column->id }}) }">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold text-gray-700">{{ $column->name }}</h3>
                    @if($isCreator)
                    <div class="flex items-center gap-1 transition-opacity">
                        <button type="button" @click="openEditColumnModal({{ $column->toJson() }})" class="p-1 hover:bg-gray-200 rounded text-gray-400 hover:text-[#3f8caf] transition-colors" title="Edit Column">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </button>
                        <button type="button" @click="openDeleteColumnModal({{ $column->toJson() }})" class="p-1 hover:bg-gray-200 rounded text-gray-400 hover:text-red-600 transition-colors" title="Delete Column">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path></svg>
                        </button>
                    </div>
                    @endif
                </div>
                @php
                    $colTasks = $tasks->where('status', $column->name);
                @endphp
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full task-count">{{ $colTasks->count() }}</span>
            </div>
            <div class="space-y-3 min-h-[100px] task-list">
                @foreach($colTasks as $task)
                @php
                    $canManageTask = auth()->user()->role === 'SuperAdmin' || 
                                     auth()->user()->role === 'Admin' || 
                                     auth()->user()->role === 'Team Leader' || 
                                     ($task->created_by === auth()->id() && auth()->user()->role !== 'Employee');
                    // Employees can drag (move) their own assigned tasks
                    $canDragTask = $canManageTask || 
                                   (auth()->user()->role === 'Employee' && $task->assigned_to === auth()->id());
                @endphp
                <div class="relative group">
                    <a href="{{ route('tasks.show', $task->id) }}" id="task-{{ $task->id }}" class="block bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow task-card {{ $task->status == 'Completed' ? 'opacity-75' : '' }}"
                        draggable="{{ $canDragTask ? 'true' : 'false' }}"
                        @dragstart="if ({{ $canDragTask ? 'true' : 'false' }}) { draggedTask = {{ $task->id }} }">
                        <div class="flex items-start justify-between mb-2 gap-2">
                            <h4 class="font-medium text-gray-900 group-hover:text-[#3f8caf] transition-colors {{ $task->status == 'Completed' ? 'line-through' : '' }} flex-1 min-w-0 break-words pr-2">{{ $task->title }}</h4>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($task->status == 'Completed') bg-green-100 text-green-700
                                    @elseif($task->priority == 'High') bg-orange-100 text-orange-700
                                    @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $task->status == 'Completed' ? 'Done' : $task->priority }}
                                </span>
                                @if($canManageTask)
                                <button type="button" @click.prevent.stop="openEditModal({{ $task->toJson() }})" class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-[#3f8caf] transition-colors" title="Edit Task">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                </button>
                                @endif
                            </div>
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
                </div>
                @endforeach
                <div class="text-center py-8 text-gray-400 text-sm no-tasks" style="display: {{ $colTasks->count() === 0 ? 'block' : 'none' }}">No tasks</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

