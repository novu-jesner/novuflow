@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-2xl mx-auto" x-data="{
    async updateProject(e) {
        await submitForm(e.target, { resetForm: false });
    },
    async deleteProject() {
        await ajaxDelete('{{ route('projects.destroy', $project->id) }}', {
            onSuccess: (data) => {
                if (data.redirect) window.location.href = data.redirect;
            }
        });
    }
}">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Edit Project</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('projects.update', $project->id) }}" method="POST" class="p-6 space-y-6" @submit.prevent="updateProject">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label for="name" class="text-sm font-medium text-gray-700">Project Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $project->name) }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >
            </div>

            <div class="space-y-2">
                <label for="description" class="text-sm font-medium text-gray-700">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium text-gray-700">Status *</label>
                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                    >
                        <option value="Active" {{ old('status', $project->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="On Hold" {{ old('status', $project->status) == 'On Hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="team_id" class="text-sm font-medium text-gray-700">Team</label>
                    <select
                        id="team_id"
                        name="team_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                    >
                        <option value="">No Team</option>
                        @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id', $project->team_id) == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="start_date" class="text-sm font-medium text-gray-700">Start Date *</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                    >
                </div>

                <div class="space-y-2">
                    <label for="due_date" class="text-sm font-medium text-gray-700">Due Date *</label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        value="{{ old('due_date', $project->due_date->format('Y-m-d')) }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                    >
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t">
                <button
                    type="button"
                    @click="deleteProject()"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
                >
                    Delete Project
                </button>

                <div class="flex gap-3">
                    <a
                        href="{{ route('projects.index') }}"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors"
                    >
                        Update Project
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
