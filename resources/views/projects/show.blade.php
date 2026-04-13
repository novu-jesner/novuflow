     @extends('layouts.app')

@section('content')
<div class="h-full flex flex-col" x-data="{ editingColumn: null }">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-end shrink-0">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $project->name }}</h1>
           
        </div>
    </div>

    <!-- Board Container -->
    <div id="board-container" class="flex-1 min-h-0 overflow-x-auto flex items-start space-x-5 pb-4 pt-1 px-1 custom-scrollbar">
        
        @foreach($project->columns as $column)
        <!-- Column -->
        <div class="column-container w-[340px] shrink-0 flex flex-col rounded-xl bg-gray-200/50 dark:bg-gray-800/40 border border-gray-200/80 dark:border-gray-700/60 shadow-sm transition-all max-h-full" 
             draggable="true"
             ondragstart="dragColumn(event)"
             ondragover="allowDropColumn(event)"
             ondrop="dropColumn(event)"
             data-column-id="{{ $column->id }}">
            
            <!-- Column Header -->
            <div class="p-3 mb-1 flex justify-between items-center group cursor-move shrink-0" title="Drag to reorder list">
                <!-- Title Display -->
                <div class="flex-1 flex flex-wrap items-center gap-2 overflow-hidden" x-show="editingColumn !== {{ $column->id }}">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors text-sm uppercase tracking-wide truncate cursor-pointer" @click.stop="editingColumn = {{ $column->id }}; $nextTick(() => { $refs['colInput' + {{ $column->id }}].focus() })">
                        {{ $column->name }}
                    </h3>
                    <span class="text-xs bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full font-bold shadow-sm border border-gray-200 dark:border-gray-600">{{ $column->tasks->count() }}</span>
                </div>

                <!-- Title Edit Form -->
                <form action="{{ route('columns.update', $column) }}" method="POST" class="flex-1 flex gap-1 mr-2" x-show="editingColumn === {{ $column->id }}" x-cloak @click.away="editingColumn = null">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="name" value="{{ $column->name }}" x-ref="colInput{{ $column->id }}" required class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1.5 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" @click.stop>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors shadow-sm" @click.stop>Save</button>
                </form>

                <div class="flex items-center pl-2">
                    <!-- Delete Column Form -->
                    <form action="{{ route('columns.destroy', $column) }}" method="POST" onsubmit="return confirm('Delete this list and all its tasks?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="opacity-0 group-hover:opacity-100 p-1.5 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-500/10 transition-all focus:outline-none" title="Delete List" @click.stop>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Task List Area -->
            <div class="flex-1 px-3 space-y-3 overflow-y-auto task-list transition-colors duration-200 min-h-[120px] cursor-default custom-scrollbar mb-1" 
                data-column-id="{{ $column->id }}"
                ondragover="allowDrop(event)" 
                ondrop="drop(event)">
                
                @foreach($column->tasks as $task)
                <div class="bg-white dark:bg-gray-700 p-4 rounded-xl shadow-sm border border-gray-200/80 dark:border-gray-600 cursor-grab hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md hover:-translate-y-0.5 group transition-all duration-200"
                    draggable="true" 
                    ondragstart="drag(event)" 
                    onclick="openTaskDetails(this.getAttribute('data-id'))"
                    data-id="{{ $task->id }}">
                    <div class="flex justify-between items-start gap-2">
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-medium leading-snug">{{ $task->title }}</p>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this card?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="opacity-0 group-hover:opacity-100 p-1 mt-0.5 rounded text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all focus:outline-none" title="Delete Task" @click.stop>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </form>
                    </div>
                    @if($task->assignee || $task->assigned_to)
                    <div class="mt-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>{{ optional($task->assignee)->name ?? $task->assigned_to }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Add Task Button -->
            <div class="p-2 shrink-0 border-t border-gray-200/50 dark:border-gray-700/50 mx-1">
                <button type="button" onclick="openTaskModal('{{ $column->id }}')" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-300/40 dark:hover:bg-gray-700/60 rounded-lg transition-colors group">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add a Task
                </button>
            </div>
        </div>
        @endforeach

        <!-- Add New Column -->
        <div class="w-[340px] shrink-0" x-data="{ openNewCol: false }">
            <button x-show="!openNewCol" @click="openNewCol = true" class="w-full flex items-center gap-2 px-4 py-3.5 rounded-xl bg-gray-200/40 dark:bg-gray-800/30 hover:bg-gray-200/80 dark:hover:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-medium text-sm transition-all border border-transparent">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add another list
            </button>

            <form x-show="openNewCol" x-cloak class="bg-gray-100/90 dark:bg-gray-800/90 p-3 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 flex flex-col gap-3" action="{{ route('columns.store', $project) }}" method="POST" @click.away="openNewCol = false">
                @csrf
                <input type="text" name="name" placeholder="Enter list title..." required class="text-sm w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors">Add List</button>
                    <button type="button" @click="openNewCol = false" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- TASK MODAL -->
<div id="taskModal" class="fixed inset-0 bg-gray-900/50 dark:bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-all opacity-0 scale-95 p-4 sm:p-6" style="transition: opacity 0.2s ease-out, transform 0.2s ease-out;">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-[1000px] max-w-[95vw] p-6 sm:p-7 shadow-2xl border border-gray-100 dark:border-gray-700 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 id="taskModalTitle" class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Add Task</h2>
                <div id="taskCreator" class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span id="taskCreatorName"></span>
                </div>
            </div>
            <button type="button" onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="taskForm" method="POST" action="{{ route('tasks.store', $project) }}" class="space-y-5">
            @csrf
            <div id="methodContainer"></div>
            <input type="hidden" name="column_id" id="modal_column_id">

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Title</label>
                <input type="text" name="title" placeholder="What needs to be done?" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-2.5 transition-colors">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                <textarea name="description" rows="3" placeholder="Add more details..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-2.5 resize-none transition-colors"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Priority</label>
                    <select name="priority" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-2.5 cursor-pointer transition-colors">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Due Date</label>
                    <input type="date" name="due_date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-2.5 cursor-pointer transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Assignee</label>
                <input type="text" name="assigned_to" placeholder="e.g. Denz Pascua" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-2.5 transition-colors">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeTaskModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">Cancel</button>
                <button type="submit" id="taskSubmitBtn" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">Add</button>
            </div>
        </form>
    </div>
</div>



<script>
function showModal(modalNode) {
    modalNode.classList.remove('hidden');
    // Frame delay to allow display to apply
    setTimeout(() => {
        modalNode.classList.remove('opacity-0', 'scale-95');
        modalNode.classList.add('opacity-100', 'scale-100');
    }, 10);
}

function hideModal(modalNode) {
    modalNode.classList.remove('opacity-100', 'scale-100');
    modalNode.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        modalNode.classList.add('hidden');
    }, 200);
}

const defaultTaskAction = `{{ route('tasks.store', $project) }}`;

function openTaskDetails(taskId) {
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const methodContainer = document.getElementById('methodContainer');
    
    document.getElementById('taskModalTitle').innerHTML = 'Edit Task';
    document.getElementById('taskSubmitBtn').innerHTML = 'Save';
    
    form.action = `/tasks/${taskId}`;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PATCH">';
    
    form.reset();

    modal.classList.add('flex');
    showModal(modal);

    fetch(`/tasks/${taskId}`)
        .then(res => res.json())
        .then(task => {
            form.elements['title'].value = task.title || '';
            form.elements['description'].value = task.description || '';
            form.elements['priority'].value = task.priority || 'low';
            if (task.due_date) {
                form.elements['due_date'].value = task.due_date.split(' ')[0] || task.due_date.split('T')[0];
            } else {
                form.elements['due_date'].value = '';
            }
            form.elements['assigned_to'].value = task.assigned_to || '';
                    const creatorEl = document.getElementById('taskCreator');
                    const creatorNameEl = document.getElementById('taskCreatorName');
                    if (task.creator_name) {
                        creatorNameEl.textContent = task.creator_name;
                        creatorEl.classList.remove('hidden');
                    } else {
                        creatorNameEl.textContent = '';
                        creatorEl.classList.add('hidden');
                    }
        }).catch(err => {
            console.error('Failed to load task details.', err);
        });
}

function openTaskModal(columnId) {
    const modal = document.getElementById('taskModal');
    const form = document.getElementById('taskForm');
    const methodContainer = document.getElementById('methodContainer');

    document.getElementById('taskModalTitle').innerHTML = 'Add Task';
    document.getElementById('taskSubmitBtn').innerHTML = 'Add';
    
    form.action = defaultTaskAction;
    methodContainer.innerHTML = '';
    form.reset();
    // Hide creator info for new task
    const creatorEl = document.getElementById('taskCreator');
    const creatorNameEl = document.getElementById('taskCreatorName');
    if (creatorEl) { creatorEl.classList.add('hidden'); creatorNameEl.textContent = ''; }
    
    document.getElementById('modal_column_id').value = columnId;
    
    modal.classList.add('flex');
    showModal(modal);
}

function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    hideModal(modal);
    setTimeout(() => { modal.classList.remove('flex'); }, 200);
}

// -- CARD DRAG AND DROP --
function allowDrop(ev) {
    if(ev.dataTransfer.types.includes("card")) {
        ev.preventDefault();
        let target = ev.currentTarget;
        if (!target.classList.contains('bg-gray-200')) {
            target.classList.add('bg-gray-200', 'dark:bg-gray-600/50');
        }
    }
}

document.querySelectorAll('.task-list').forEach(list => {
    list.addEventListener('dragleave', function(ev) {
        this.classList.remove('bg-gray-200', 'dark:bg-gray-600/50');
    });
});

function drag(ev) {
    ev.stopPropagation(); 
    ev.dataTransfer.setData("card", ev.target.getAttribute('data-id'));
    ev.target.classList.add('opacity-50', 'scale-95');
}

document.querySelectorAll('.task-list [draggable="true"]').forEach(card => {
    card.addEventListener('dragend', function(ev) {
        this.classList.remove('opacity-50', 'scale-95');
    });
});

function drop(ev) {
    if(ev.dataTransfer.types.includes("card")) {
        ev.preventDefault();
        ev.stopPropagation();
        let targetList = ev.currentTarget;
        targetList.classList.remove('bg-gray-200', 'dark:bg-gray-600/50');
        
        let taskId = ev.dataTransfer.getData("card");
        let newColumnId = targetList.getAttribute('data-column-id');
        let card = document.querySelector(`.task-list [data-id='${taskId}']`);

        if(card && card.parentElement !== targetList) {
            targetList.appendChild(card);
            updateCounts();
            
            fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ column_id: newColumnId })
            }).then(response => {
                if(!response.ok) {
                    alert('Error updating task location.');
                    window.location.reload();
                }
            });
        }
    }
}

function updateCounts() {
    document.querySelectorAll('.task-list').forEach(list => {
        let count = list.querySelectorAll('.task-list [draggable="true"]').length;
        let countBadge = list.parentElement.querySelector('.rounded-full');
        if(countBadge) {
            countBadge.textContent = count;
        }
    });
}

// -- COLUMN DRAG AND DROP --
function dragColumn(ev) {
    ev.dataTransfer.setData("column", ev.currentTarget.getAttribute('data-column-id'));
    ev.currentTarget.classList.add('opacity-50', 'scale-95');
}

function allowDropColumn(ev) {
    if(ev.dataTransfer.types.includes("column")) {
        ev.preventDefault(); 
    } else if (ev.dataTransfer.types.includes("card")) {
        ev.preventDefault();
        let targetList = ev.currentTarget.querySelector('.task-list');
        if (targetList && !targetList.classList.contains('bg-gray-200')) {
            targetList.classList.add('bg-gray-200', 'dark:bg-gray-600/50');
        }
    }
}

document.querySelectorAll('.column-container').forEach(col => {
    col.addEventListener('dragend', function(ev) {
        this.classList.remove('opacity-50', 'scale-95');
        this.classList.remove('border-indigo-500', 'dark:border-indigo-400');
    });
    
    col.addEventListener('dragover', function(ev) {
        if(ev.dataTransfer.types.includes("column")) {
            this.classList.add('border-indigo-500', 'dark:border-indigo-400');
        }
    });

    col.addEventListener('dragleave', function(ev) {
        this.classList.remove('border-indigo-500', 'dark:border-indigo-400');
        let taskList = this.querySelector('.task-list');
        if (taskList) {
            taskList.classList.remove('bg-gray-200', 'dark:bg-gray-600/50');
        }
    });
});

function dropColumn(ev) {
    if(ev.dataTransfer.types.includes("column")) {
        ev.preventDefault();
        ev.stopPropagation();
        let targetColumn = ev.currentTarget;
        targetColumn.classList.remove('border-indigo-500', 'dark:border-indigo-400');
        
        let sourceId = ev.dataTransfer.getData("column");
        let sourceColumn = document.querySelector(`.column-container[data-column-id='${sourceId}']`);
        
        if (sourceColumn && sourceColumn !== targetColumn) {
            let allColumns = Array.from(document.querySelectorAll('.column-container'));
            let sourceIndex = allColumns.indexOf(sourceColumn);
            let targetIndex = allColumns.indexOf(targetColumn);
            
            if (sourceIndex < targetIndex) {
                targetColumn.after(sourceColumn);
            } else {
                targetColumn.before(sourceColumn);
            }
            
            let orderedIds = Array.from(document.querySelectorAll('.column-container'))
                                 .map(c => c.getAttribute('data-column-id'));
            
            fetch(`/projects/{{ $project->id }}/columns/reorder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ columns: orderedIds })
            }).then(response => {
                if(!response.ok) {
                    alert('Error saving column order.');
                    window.location.reload();
                }
            });
        }
    } else if (ev.dataTransfer.types.includes("card")) {
        ev.preventDefault();
        ev.stopPropagation();
        
        let targetList = ev.currentTarget.querySelector('.task-list');
        if (targetList) {
            targetList.classList.remove('bg-gray-200', 'dark:bg-gray-600/50');
            
            let taskId = ev.dataTransfer.getData("card");
            let newColumnId = targetList.getAttribute('data-column-id');
            let card = document.querySelector(`.task-list [data-id='${taskId}']`);

            if(card && card.parentElement !== targetList) {
                targetList.appendChild(card);
                updateCounts();
                
                fetch(`/tasks/${taskId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ column_id: newColumnId })
                }).then(response => {
                    if(!response.ok) {
                        alert('Error updating task location.');
                        window.location.reload();
                    }
                });
            }
        }
    }
}
</script>
@endsection
