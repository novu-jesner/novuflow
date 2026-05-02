@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ 
        filter: 'all', 
        searchQuery: '', 
        showModal: false,
        async createProject(e) {
            await submitForm(e.target, {
                resetForm: true,
                onSuccess: (data) => {
                    this.showModal = false;
                    if (data.redirect) window.location.href = data.redirect;
                    else window.location.reload();
                }
            });
        }
    }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Projects</h1>
            <p class="text-gray-600 mt-1">Manage and track all your projects</p>
        </div>
        <button @click="showModal = true" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
            New Project
        </button>
    </div>

    <!-- Create Project Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
        <div style="display: flex; min-height: 100%; align-items: center; justify-content: center; padding: 1rem;">
            <div style="position: relative; transform: none; overflow: hidden; border-radius: 0.5rem; background-color: white; text-align: left; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); width: 100%; max-width: 32rem;">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4">Create New Project</h3>
                    <form id="createProjectForm" action="{{ route('projects.store') }}" method="POST" class="space-y-4" @submit.prevent="createProject($event)">
                        @csrf
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">Project Name</label>
                            <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                        </div>
                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="Active">Active</option>
                                    <option value="On Hold">On Hold</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label for="team_id" class="block text-sm font-medium text-gray-700">Team</label>
                                <select id="team_id" name="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                                    <option value="">No Team</option>
                                    @foreach(\App\Models\Team::all() as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" id="start_date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                            </div>
                            <div class="space-y-2">
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" id="due_date" name="due_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="submit" form="createProjectForm" class="inline-flex justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">Create Project</button>
                    <button type="button" @click="showModal = false" class="inline-flex justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-4">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input
                type="text"
                placeholder="Search projects..."
                x-model="searchQuery"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
            >
        </div>
        <select x-model="filter" class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
            <option value="all">All Projects</option>
            <option value="Active">Active</option>
            <option value="On Hold">On Hold</option>
            <option value="Completed">Completed</option>
        </select>
    </div>

    <!-- Projects Grid -->
    @forelse($projects as $project)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6 border-b">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <h3 class="font-semibold truncate">{{ $project->name }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $project->description }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full text-white
                        @if($project->status == 'Active') bg-blue-500
                        @elseif($project->status == 'On Hold') bg-yellow-500
                        @elseif($project->status == 'Completed') bg-green-500
                        @else bg-gray-500 @endif">
                        {{ $project->status }}
                    </span>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <!-- Progress -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>

                <!-- Dates -->
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                        </svg>
                        <span>{{ $project->start_date->format('M d, Y') }}</span>
                    </div>
                    <span>→</span>
                    <span>{{ $project->due_date->format('M d, Y') }}</span>
                </div>

                <!-- Team Members -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <div class="flex -space-x-2">
                            @php $memberCount = $project->members->count(); @endphp
                            @for($i = 0; $i < min(4, $memberCount); $i++)
                                <div class="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center text-white text-xs
                                    @if($i == 0) bg-blue-500
                                    @elseif($i == 1) bg-green-500
                                    @elseif($i == 2) bg-purple-500
                                    @else bg-orange-500 @endif">
                                    {{ chr(65 + $i) }}
                                </div>
                            @endfor
                            @if($memberCount > 4)
                                <div class="w-7 h-7 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs">+{{ $memberCount - 4 }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('projects.show', $project->id) }}" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">No projects found. Create your first project!</p>
        </div>
    @endforelse
</div>
@endsection
