     @extends('layouts.app')

@section('content')
<style>
    /* Manual Dark Mode Overrides for Task Modal */
    .dark #taskViewModal .bg-white, 
    .dark #taskModal .bg-white {
        background-color: #1f2937 !important; /* gray-800 */
        color: #f9fafb !important; /* gray-50 */
    }
    
    .dark #viewDescription,
    .dark .comment-item,
    .dark #editCommentsList div {
        background-color: #374151 !important; /* gray-700 */
        color: #d1d5db !important; /* gray-300 */
        border-color: #4b5563 !important; /* gray-600 */
    }

    .dark #taskViewModal h2, 
    .dark #taskViewModal h3,
    .dark #taskModal h2,
    .dark #taskModal h3,
    .dark #viewTitle {
        color: #ffffff !important;
    }

    .dark .text-gray-400, .dark .text-gray-500 {
        color: #9ca3af !important; /* gray-400 */
    }

    .dark #commentForm textarea {
        background-color: #374151 !important;
        color: white !important;
        border-color: #4b5563 !important;
    }

    /* Badge Dark Mode Overrides */
    .dark .bg-red-100, .dark .bg-red-50 { 
        background-color: rgba(153, 27, 27, 0.4) !important; 
        color: #fecaca !important; 
    }
    .dark .bg-yellow-100 { 
        background-color: rgba(133, 77, 14, 0.4) !important; 
        color: #fef08a !important; 
    }
    .dark .bg-green-100 { 
        background-color: rgba(6, 78, 59, 0.4) !important; 
        color: #a7f3d0 !important; 
    }
    .dark .bg-gray-100, .dark .bg-gray-800 { 
        background-color: #374151 !important; 
        color: #d1d5db !important; 
    }
</style>
<div class="h-full flex flex-col" x-data="{ editingColumn: null, managingMembers: false }">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-end shrink-0">
        <div>
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $project->name }}</h1>
        </div>
            @if(count($projectMemberIds) > 0)
            <div class="mt-2 flex items-center gap-2 flex-wrap">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Members:</span>
                @foreach($project->members as $pm)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>{{ $pm->name }}
                    </span>
                @endforeach
            </div>
            @endif
        </div>
        @if(auth()->guard('web')->check() && in_array(auth()->guard('web')->user()->role, ['team_lead', 'admin', 'super_admin']))
        <button type="button" @click="managingMembers = true"
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Manage Members
        </button>
        @endif
    </div>

    <!-- Manage Members Modal -->
    <div x-show="managingMembers" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-md p-6"
             @click.away="managingMembers = false">
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Manage Project Members</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Select who can be involved in <strong>{{ $project->name }}</strong></p>
                </div>
                <button type="button" @click="managingMembers = false" class="p-1.5 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('projects.members.sync', $project) }}" method="POST">
                @csrf
                @if($members->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">No team members found.</p>
                @else
                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($members as $member)
                        <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                {{ in_array($member->id, $projectMemberIds) ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-indigo-500 cursor-pointer">
                            <div class="flex-1 min-w-0">
                                <span class="block text-sm font-medium text-gray-800 dark:text-gray-200">{{ $member->name }}</span>
                                @if($member->position)
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $member->position }}</span>
                                @endif
                            </div>
                            @if(in_array($member->id, $projectMemberIds))
                            <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">Assigned</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                @endif
                <div class="flex justify-end gap-3 mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="managingMembers = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition-colors">
                        Save Members
                    </button>
                </div>
            </form>
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
                    onclick="openTaskView(this.getAttribute('data-id'))"
                    data-id="{{ $task->id }}">

                    @php
                        $priority = $task->priority ?? 'low';
                        $isDue = false;
                        if ($task->due_date) {
                            try {
                                $due = \Carbon\Carbon::parse($task->due_date)->startOfDay();
                                $today = \Carbon\Carbon::today();
                                if ($due->lte($today)) { $isDue = true; }
                            } catch (\Exception $e) { $isDue = false; }
                        }
                        // Priority badge color depends only on priority.
                        $priorityClass = $priority === 'high'
                            ? 'bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-200'
                            : ($priority === 'medium'
                                ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-200'
                                : 'bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-200');
                        // Dot color follows priority; due state shows a subtle ring instead.
                        $priorityDotClass = $priority === 'high' ? 'bg-red-600' : ($priority === 'medium' ? 'bg-yellow-500' : 'bg-green-600');
                    @endphp

                    <div class="flex justify-between items-start gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start gap-2">
                                <span class="flex-shrink-0 mt-1 w-2.5 h-2.5 rounded-full {{ $priorityDotClass }} {{ $isDue ? 'ring-2 ring-red-400/30' : '' }}"></span>
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-medium leading-snug truncate">{{ $task->title }}</p>
                            </div>

                            @if($task->description)
                                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed truncate">{{ $task->description }}</p>
                            @endif

                            <div class="mt-3 flex items-center gap-3 text-xs">
                                @if($task->due_date)
                                    <span class="px-2 py-1 rounded-md {{ $isDue ? 'bg-red-50 text-red-800 dark:bg-red-800/20 dark:text-red-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}</span>
                                @endif
                                <span class="px-2 py-1 rounded-md {{ $priorityClass }}">Priority: {{ ucfirst($priority) }}</span>
                            </div>
                        </div>

                        @if(!Auth::guard('member')->check())
                        <div class="flex items-center">
                            <button type="button" onclick="event.stopPropagation(); openTaskDetails('{{ $task->id }}')" class="opacity-0 group-hover:opacity-100 p-1 mt-0.5 mr-1 rounded text-gray-400 hover:text-indigo-500 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all focus:outline-none" title="Edit Task">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"></path></svg>
                            </button>

                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this card?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="opacity-0 group-hover:opacity-100 p-1 mt-0.5 rounded text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all focus:outline-none" title="Delete Task" @click.stop>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    <div class="mt-2.5 flex items-center justify-between border-t border-gray-50 dark:border-gray-600/50 pt-2">
                        <!-- Assignee -->
                        @if($task->assignee || $task->assigned_to)
                        <div class="flex items-center gap-1.5 text-[10px] font-medium text-gray-500 dark:text-gray-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>{{ optional($task->assignee)->name ?? $task->assigned_to }}</span>
                        </div>
                        @else
                        <div></div>
                        @endif

                        <!-- Creator -->
                        @php
                            $creatorName = $task->creator->name ?? ($task->memberCreator->name ?? 'System');
                        @endphp
                        <div class="flex items-center gap-1 text-[10px] text-gray-400 dark:text-gray-500 italic">
                            <span>By: {{ $creatorName }}</span>
                        </div>
                    </div>
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
                @php
                    // Show only project members if any are assigned; otherwise fall back to all team members
                    $assignableMembers = count($projectMemberIds) > 0
                        ? $members->filter(fn($m) => in_array($m->id, $projectMemberIds))
                        : $members;
                @endphp
                <select name="assigned_to" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-2.5 cursor-pointer transition-colors">
                    <option value="">Unassigned</option>
                    @foreach($assignableMembers as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Added Comments to Edit Modal for consistency -->
            <div id="editModalComments" class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 hidden">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Comments</h3>
                <div id="editCommentsList" class="space-y-3 mb-4 max-h-[200px] overflow-y-auto custom-scrollbar"></div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="closeTaskModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors shadow-sm">Cancel</button>
                <button type="submit" id="taskSubmitBtn" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">Add</button>
            </div>
        </form>
    </div>
</div>

    <!-- TASK VIEW MODAL -->
    <div id="taskViewModal" class="fixed inset-0 bg-gray-900/50 dark:bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-all opacity-0 scale-95 p-4 sm:p-6" style="transition: opacity 0.2s ease-out, transform 0.2s ease-out;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-[1000px] max-w-[95vw] p-6 sm:p-7 shadow-2xl border border-gray-100 dark:border-gray-700 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 id="taskViewTitle" class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Task Details</h2>
                    <div id="taskViewCreator" class="mt-1 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span id="taskViewCreatorName"></span>
                    </div>
                </div>
                <button type="button" onclick="closeTaskViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full p-1.5 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        
            <div id="taskViewBody" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Title</label>
                    <p id="viewTitle" class="w-full text-lg font-bold text-gray-900 dark:text-white"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
                    <p id="viewDescription" class="w-full rounded-lg text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-gray-700/30 p-3"></p>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Priority</label>
                        <p id="viewPriority" class="text-sm font-medium text-gray-700 dark:text-gray-200"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Due Date</label>
                        <p id="viewDueDate" class="text-sm font-medium text-gray-700 dark:text-gray-200"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Assignee</label>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-[10px] font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <p id="viewAssignedTo" class="text-sm font-medium text-gray-700 dark:text-gray-200"></p>
                    </div>
                </div>

                <!-- Comments Section (Bottom Just like Edit Modal) -->
                <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">Comments</label>
                    
                    <div id="commentsList" class="space-y-3 mb-6 max-h-[300px] overflow-y-auto custom-scrollbar pr-2">
                        <!-- Comments will be rendered here -->
                    </div>

                    <form id="commentForm" method="POST" action="" class="mt-6">
                        @csrf
                        <div class="space-y-3">
                            <textarea name="content" required placeholder="Add a comment..." rows="3" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm px-4 py-3 resize-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-500"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                                    Post Comment
                                </button>
                            </div>
                        </div>
                    
                    </form>
                    <!-- Activity Log Section -->
<div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-4">
        Activity
    </label>

    <div id="taskActivityList" class="space-y-2 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
        <p class="text-sm text-gray-400 italic text-center py-3">Loading activity...</p>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>



<script>


function escapeHtml(str) {
    return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
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
    const editCommentsSection = document.getElementById('editModalComments');
    const editCommentsList = document.getElementById('editCommentsList');
    
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
            
            // Show comments in Edit modal
            if (task.comments && task.comments.length > 0) {
                editCommentsSection.classList.remove('hidden');
                editCommentsList.innerHTML = '';
                task.comments.forEach(comment => {
                    const div = document.createElement('div');
                    div.className = 'bg-gray-100 dark:bg-gray-700/50 p-2 rounded-lg text-xs text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600';
                    div.innerHTML = `<strong class="text-indigo-600 dark:text-indigo-400">${comment.author}:</strong> ${comment.content}`;
                    editCommentsList.appendChild(div);
                });
            } else {
                editCommentsSection.classList.add('hidden');
            }

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
function openTaskView(taskId) {
    const modal = document.getElementById('taskViewModal');

    const titleEl = document.getElementById('viewTitle');
    const descEl = document.getElementById('viewDescription');
    const priorityEl = document.getElementById('viewPriority');
    const dueEl = document.getElementById('viewDueDate');
    const assignedEl = document.getElementById('viewAssignedTo');
    const creatorEl = document.getElementById('taskViewCreator');
    const creatorNameEl = document.getElementById('taskViewCreatorName');
    const commentsList = document.getElementById('commentsList');
    const commentForm = document.getElementById('commentForm');
    const activityList = document.getElementById('taskActivityList'); // ✅ include here

    document.getElementById('taskViewTitle').innerHTML = 'Task Details';

    // reset UI state
    titleEl.textContent = '';
    descEl.textContent = '';
    priorityEl.textContent = '';
    dueEl.textContent = '';
    assignedEl.textContent = '';
    creatorNameEl.textContent = '';
    creatorEl.classList.add('hidden');

    commentsList.innerHTML = '<p class="text-xs text-gray-400 italic">Loading comments...</p>';
    activityList.innerHTML = '<p class="text-xs text-gray-400 italic">Loading activity...</p>'; // ✅ important reset

    commentForm.action = `/tasks/${taskId}/comments`;

    modal.classList.add('flex');
    showModal(modal);

    fetch(`/tasks/${taskId}`)
        .then(res => res.json())
        .then(task => {

            /* =========================
               BASIC TASK DATA
            ========================== */
            titleEl.textContent = task.title || '';
            descEl.textContent = task.description || '';
            priorityEl.textContent = task.priority
                ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1)
                : '';

            dueEl.textContent = task.due_date
                ? new Date(task.due_date).toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                })
                : 'No due date';

            assignedEl.textContent =
                task.assigned_to_name || task.assigned_to || 'Unassigned';

            if (task.creator_name) {
                creatorNameEl.textContent = task.creator_name;
                creatorEl.classList.remove('hidden');
            }

            /* =========================
               COMMENTS
            ========================== */
            commentsList.innerHTML = '';

            if (task.comments?.length) {
                task.comments.forEach(comment => {
                    const div = document.createElement('div');
                    div.className =
                        'comment-item bg-gray-100 dark:bg-gray-700/50 p-2.5 rounded-lg text-sm text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600';

                    // ⚠️ safer than innerHTML injection
                    div.innerHTML = `
                        <strong class="text-indigo-600 dark:text-indigo-400">
                            ${escapeHtml(comment.author)}
                        </strong>: ${escapeHtml(comment.content)}
                    `;

                    commentsList.appendChild(div);
                });

                setTimeout(() => {
                    commentsList.scrollTop = commentsList.scrollHeight;
                }, 100);
            } else {
                commentsList.innerHTML =
                    '<p class="text-sm text-gray-400 italic text-center py-4">No comments yet.</p>';
            }

            /* =========================
               ACTIVITY LOG (FIXED)
            ========================== */
            activityList.innerHTML = '';

            if (task.activities?.length) {
                task.activities.forEach(log => {
                    const div = document.createElement('div');
                    div.className =
                        'text-xs text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/40 p-2 rounded-lg';

                    div.innerHTML = `
                        <div class="font-semibold text-indigo-500">
                            ${escapeHtml(log.action)}
                        </div>
                        <div>${escapeHtml(log.description)}</div>
                        <div class="text-[10px] text-gray-400">
                            ${escapeHtml(log.date)}
                        </div>
                    `;

                    activityList.appendChild(div);
                });
            } else {
                activityList.innerHTML =
                    '<p class="text-sm text-gray-400 italic text-center py-3">No activity yet</p>';
            }
        })
        .catch(err => {
            console.error('Failed to load task details.', err);

            commentsList.innerHTML =
                '<p class="text-sm text-red-400 italic text-center py-4">Error loading comments.</p>';

            activityList.innerHTML =
                '<p class="text-sm text-red-400 italic text-center py-4">Error loading activity.</p>';
        });
}

function closeTaskViewModal() {
    const modal = document.getElementById('taskViewModal');
    hideModal(modal);
    setTimeout(() => { modal.classList.remove('flex'); }, 200);
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
<!-- Tailwind safelist: ensure dynamic priority classes are included in build -->
<div class="hidden" aria-hidden="true">
    <span class="bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-200 bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-200 bg-green-600 bg-yellow-500 bg-red-600 bg-red-50 text-red-800"></span>
</div>
@endsection
