@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ filter: 'all', searchQuery: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Projects</h1>
            <p class="text-gray-600 mt-1">Manage and track all your projects</p>
        </div>
        <button class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
            New Project
        </button>
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
            <option value="Planning">Planning</option>
            <option value="In Progress">In Progress</option>
            <option value="On Hold">On Hold</option>
            <option value="Completed">Completed</option>
        </select>
    </div>

    <!-- Projects Grid -->
    <template x-for="project in [
        { id: '1', title: 'Website Redesign', description: 'Complete overhaul of company website with new branding', status: 'In Progress', progress: 75, startDate: '2024-01-01', dueDate: '2024-12-31', teamMembers: 5 },
        { id: '2', title: 'Mobile App Development', description: 'Native mobile application for iOS and Android', status: 'In Progress', progress: 45, startDate: '2024-02-01', dueDate: '2025-01-15', teamMembers: 4 },
        { id: '3', title: 'Marketing Campaign', description: 'Q1 marketing campaign for product launch', status: 'Planning', progress: 15, startDate: '2024-03-01', dueDate: '2024-06-30', teamMembers: 3 },
        { id: '4', title: 'API Integration', description: 'Integration with third-party payment APIs', status: 'Completed', progress: 100, startDate: '2023-10-01', dueDate: '2023-12-31', teamMembers: 2 },
    ].filter(p => (filter === 'all' || p.status === filter) && p.title.toLowerCase().includes(searchQuery.toLowerCase()))" :key="project.id">
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6 border-b">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <h3 class="font-semibold truncate" x-text="project.title"></h3>
                        <p class="text-sm text-gray-600 line-clamp-2" x-text="project.description"></p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full text-white" :class="{
                        'bg-gray-500': project.status === 'Planning',
                        'bg-blue-500': project.status === 'In Progress',
                        'bg-yellow-500': project.status === 'On Hold',
                        'bg-green-500': project.status === 'Completed'
                    }" x-text="project.status"></span>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <!-- Progress -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Progress</span>
                        <span class="font-medium" x-text="project.progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] h-2 rounded-full" :style="'width: ' + project.progress + '%'"></div>
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
                        <span x-text="new Date(project.startDate).toLocaleDateString()"></span>
                    </div>
                    <span>→</span>
                    <span x-text="new Date(project.dueDate).toLocaleDateString()"></span>
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
                            <template x-for="i in Math.min(4, project.teamMembers)" :key="i">
                                <div class="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center text-white text-xs" :class="'bg-' + ['blue', 'green', 'purple', 'orange'][i-1] + '-500'" x-text="String.fromCharCode(65 + i)"></div>
                            </template>
                            <template x-if="project.teamMembers > 4">
                                <div class="w-7 h-7 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs" x-text="'+' + (project.teamMembers - 4)"></div>
                            </template>
                        </div>
                    </div>
                    <a :href="'/dashboard/projects/' + project.id" class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
