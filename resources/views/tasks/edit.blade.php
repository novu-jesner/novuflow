@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-2xl mx-auto" x-data="{
    async updateTask(e) {
        await submitForm(e.target, { resetForm: false });
    },
    async deleteTask() {
        await ajaxDelete('{{ route('tasks.destroy', $task->id) }}', {
            onSuccess: (data) => {
                if (data.redirect) window.location.href = data.redirect;
            }
        });
    }
}">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('kanban.board', $task->project_id) }}" class="text-muted-foreground hover:text-foreground transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-foreground">Edit Task</h1>
    </div>

    <div class="bg-card border border-border rounded-lg shadow">
        <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="p-6 space-y-6" @submit.prevent="updateTask">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="title" class="text-sm font-medium">Task Title *</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $task->title) }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
            </div>

            <div class="space-y-2">
                <label for="description" class="text-sm font-medium">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium">Status *</label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                    >
                        <option value="To Do" {{ old('status', $task->status) == 'To Do' ? 'selected' : '' }}>To Do</option>
                        <option value="In Progress" {{ old('status', $task->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Review" {{ old('status', $task->status) == 'Review' ? 'selected' : '' }}>Review</option>
                        <option value="Completed" {{ old('status', $task->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="priority" class="text-sm font-medium">Priority *</label>
                    <select
                        id="priority"
                        name="priority"
                        required
                        class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                    >
                        <option value="Low" {{ old('priority', $task->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ old('priority', $task->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ old('priority', $task->priority) == 'High' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="project_id" class="text-sm font-medium">Project *</label>
                    <select
                        id="project_id"
                        name="project_id"
                        required
                        class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                    >
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="assigned_to" class="text-sm font-medium">Assigned To</label>
                    <select
                        id="assigned_to"
                        name="assigned_to"
                        class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                    >
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label for="due_date" class="text-sm font-medium">Due Date</label>
                <input
                    type="date"
                    id="due_date"
                    name="due_date"
                    value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-border">
                <button
                    type="button"
                    @click="deleteTask()"
                    class="px-4 py-2 bg-destructive text-destructive-foreground rounded-md hover:opacity-90 transition-opacity"
                >
                    Delete Task
                </button>

                <div class="flex gap-3">
                    <a
                        href="{{ route('tasks.show', $task->id) }}"
                        class="px-4 py-2 border border-border text-foreground rounded-md hover:bg-muted/30 transition-colors"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-primary-foreground rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity"
                    >
                        Update Task
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
