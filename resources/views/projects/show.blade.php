     @extends('layouts.app')

@section('content')
<div class="h-full flex flex-col" x-data="{ editingColumn: null }">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $project->name }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Novuflow Board</p>
    </div>

    <div id="board-container" class="flex-1 overflow-x-auto flex space-x-6 pb-4 items-start">
        
        @foreach($project->columns as $column)
        <!-- Column -->
        <div class="column-container w-80 flex-shrink-0 flex flex-col rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700/50 shadow-sm transition-all cursor-move" 
             style="max-height: calc(100vh - 12rem);"
             draggable="true"
             ondragstart="dragColumn(event)"
             ondragover="allowDropColumn(event)"
             ondrop="dropColumn(event)"
             data-column-id="{{ $column->id }}">
            
            <!-- Column Header -->
            <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center group cursor-pointer" title="Drag to reorder column">
                <!-- Title Display -->
                <div class="flex-1 mr-2" x-show="editingColumn !== {{ $column->id }}">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" @click.stop="editingColumn = {{ $column->id }}; $nextTick(() => { $refs['colInput' + {{ $column->id }}].focus() })">
                        {{ $column->name }}
                    </h3>
                </div>

                <!-- Title Edit Form -->
                <form action="{{ route('columns.update', $column) }}" method="POST" class="flex-1 flex gap-1 mr-2" x-show="editingColumn === {{ $column->id }}" x-cloak style="display: none;" @click.away="editingColumn = null">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="name" value="{{ $column->name }}" x-ref="colInput{{ $column->id }}" required class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 focus:ring-indigo-500 focus:border-indigo-500" @click.stop>
                    <button type="submit" class="bg-indigo-600 text-white px-2 py-1 rounded text-xs" @click.stop>Save</button>
                </form>

                <div class="flex items-center space-x-2">
                    <span class="text-xs bg-white/50 dark:bg-black/20 px-2 py-1 rounded-full font-bold text-gray-600 dark:text-gray-300">{{ $column->tasks->count() }}</span>
                    <!-- Delete Column Form -->
                    <form action="{{ route('columns.destroy', $column) }}" method="POST" onsubmit="return confirm('Delete this list and all its tasks?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity focus:outline-none" title="Delete Column" @click.stop>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Task List Area -->
            <div 
                class="flex-1 p-3 space-y-3 overflow-y-auto task-list transition-colors duration-200 min-h-[50px] cursor-default" 
                data-column-id="{{ $column->id }}"
                ondragover="allowDrop(event)" 
                ondrop="drop(event)">
                
                @foreach($column->tasks as $task)
                <div 
                    class="bg-white dark:bg-gray-700 p-3 rounded shadow-sm cursor-grab border border-gray-200 dark:border-gray-600 hover:border-indigo-400 dark:hover:border-indigo-500 group transition-all"
                    draggable="true" 
                    ondragstart="drag(event)" 
                    data-id="{{ $task->id }}">
                    <div class="flex justify-between items-start gap-2">
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-medium leading-tight">{{ $task->title }}</p>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this card?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity focus:outline-none" title="Delete Task" @click.stop>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Add Task Form -->
            <div class="p-3 bg-white/30 dark:bg-black/10 border-t border-gray-200 dark:border-gray-700/50 rounded-b-lg cursor-default">
                <form action="{{ route('tasks.store', $project) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="column_id" value="{{ $column->id }}">
                    <input type="text" name="title" placeholder="Add a card..." required class="text-sm w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-md px-3 py-1.5 text-sm font-medium shadow-sm transition-colors">+</button>
                </form>
            </div>
        </div>
        @endforeach

        <!-- Add New Column -->
        <div class="w-80 flex-shrink-0 rounded-lg p-2" x-data="{ openNewCol: false }">
            <button x-show="!openNewCol" @click="openNewCol = true" class="w-full text-left px-4 py-3 rounded-lg bg-gray-200/50 dark:bg-gray-800/50 hover:bg-gray-200 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium text-sm transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add another list
            </button>

            <form x-show="openNewCol" style="display: none;" class="bg-gray-100 dark:bg-gray-800 p-3 rounded-lg shadow border border-gray-200 dark:border-gray-700 flex flex-col space-y-2" action="{{ route('columns.store', $project) }}" method="POST" @click.away="openNewCol = false">
                @csrf
                <input type="text" name="name" placeholder="List title..." required class="text-sm w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors">Add List</button>
                    <button type="button" @click="openNewCol = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
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
        ev.stopPropagation(); // prevent column from dragging
        ev.dataTransfer.setData("card", ev.target.getAttribute('data-id'));
        ev.target.classList.add('opacity-50');
    }

    document.querySelectorAll('.task-list [draggable="true"]').forEach(card => {
        card.addEventListener('dragend', function(ev) {
            this.classList.remove('opacity-50');
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
        ev.currentTarget.classList.add('opacity-50');
    }

    function allowDropColumn(ev) {
        if(ev.dataTransfer.types.includes("column")) {
            ev.preventDefault(); 
        }
    }

    document.querySelectorAll('.column-container').forEach(col => {
        col.addEventListener('dragend', function(ev) {
            this.classList.remove('opacity-50');
            this.classList.remove('border-indigo-500', 'dark:border-indigo-400');
        });
        
        col.addEventListener('dragover', function(ev) {
            if(ev.dataTransfer.types.includes("column")) {
                this.classList.add('border-indigo-500', 'dark:border-indigo-400');
            }
        });

        col.addEventListener('dragleave', function(ev) {
            this.classList.remove('border-indigo-500', 'dark:border-indigo-400');
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
        }
    }
</script>
@endsection
