<a href="{{ route('kanban.board', $task->project_id) }}#task-{{ $task->id }}" class="block bg-white rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow group cursor-pointer">
    <div class="p-4 space-y-3">
        <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
                <h4 class="font-medium text-gray-900 group-hover:text-[#3f8caf] transition-colors truncate {{ $task->status == 'Completed' ? 'line-through text-gray-400' : '' }}">
                    {{ $task->title }}
                </h4>
                <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $task->description }}</p>
            </div>
            <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full shrink-0
                @if($task->priority == 'High') bg-red-100 text-red-700
                @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                @else bg-green-100 text-green-700 @endif">
                {{ $task->priority }}
            </span>
        </div>

        <div class="flex flex-wrap gap-2">
            <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 text-gray-600 rounded text-[11px] font-medium border border-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                {{ $task->status }}
            </div>
            @if($task->project)
            <div class="flex items-center gap-1.5 px-2 py-1 bg-blue-50 text-blue-600 rounded text-[11px] font-medium border border-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" x2="21" y1="9" y2="9"/><line x1="9" x2="9" y1="21" y2="9"/></svg>
                {{ $task->project->name }}
            </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-gray-50 mt-2">
            <div class="flex items-center gap-2 text-xs text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <span class="{{ $task->due_date && $task->due_date->isPast() && $task->status !== 'Completed' ? 'text-red-500 font-medium' : '' }}">
                    {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                </span>
            </div>
            <div class="flex -space-x-2">
                @if($task->assignee)
                    <div title="Assigned to: {{ $task->assignee->name }}" class="w-6 h-6 rounded-full bg-indigo-500 border-2 border-white flex items-center justify-center text-white text-[10px] font-bold">
                        {{ substr($task->assignee->name, 0, 1) }}
                    </div>
                @endif
                @if($task->creator && $task->creator->id !== ($task->assignee->id ?? null))
                    <div title="Created by: {{ $task->creator->name }}" class="w-6 h-6 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-[10px] font-bold">
                        {{ substr($task->creator->name, 0, 1) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</a>
