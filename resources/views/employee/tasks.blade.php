@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'all' }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-gray-900">My Tasks</h1>
        <p class="text-gray-600 mt-1">View and manage all your assigned tasks</p>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                To Do
            </h3>
            <div class="text-2xl font-bold">2</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" x2="12" y1="8" y2="12"></line>
                    <line x1="12" x2="12.01" y1="16" y2="16"></line>
                </svg>
                In Progress
            </h3>
            <div class="text-2xl font-bold">2</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" x2="12" y1="8" y2="12"></line>
                    <line x1="12" x2="12.01" y1="16" y2="16"></line>
                </svg>
                In Review
            </h3>
            <div class="text-2xl font-bold">1</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Completed
            </h3>
            <div class="text-2xl font-bold">3</div>
        </div>
    </div>

    <!-- Tasks Tabs -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b">
            <button @click="activeTab = 'all'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'all' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">All Tasks</button>
            <button @click="activeTab = 'todo'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'todo' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">To Do</button>
            <button @click="activeTab = 'inprogress'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'inprogress' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">In Progress</button>
            <button @click="activeTab = 'review'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'review' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Review</button>
            <button @click="activeTab = 'completed'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'completed' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Completed</button>
        </div>

        <!-- All Tasks Tab -->
        <div x-show="activeTab === 'all'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <template x-for="task in [
                { id: '1', title: 'Design homepage mockups', description: 'Create initial design concepts for the new homepage', priority: 'High', tags: ['Design', 'UI/UX'], dueDate: '2024-04-05' },
                { id: '2', title: 'Implement responsive navigation', description: 'Build mobile-friendly navigation menu with hamburger icon', priority: 'Medium', tags: ['Development', 'Frontend'], dueDate: '2024-04-10' },
                { id: '3', title: 'Set up CI/CD pipeline', description: 'Configure automated testing and deployment workflows', priority: 'Urgent', tags: ['DevOps', 'Infrastructure'], dueDate: '2024-04-03' },
                { id: '4', title: 'Write blog post about new features', description: 'Create engaging content highlighting the latest product updates', priority: 'Low', tags: ['Content', 'Marketing'], dueDate: '2024-04-08' },
                { id: '5', title: 'User testing sessions', description: 'Conduct usability testing with 10 participants', priority: 'Medium', tags: ['Research', 'UX'], dueDate: '2024-04-12' },
                { id: '6', title: 'Code review for authentication module', description: 'Review and approve the authentication implementation', priority: 'High', tags: ['Development', 'Review'], dueDate: '2024-04-06' },
            ]" :key="task.id">
                <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900" x-text="task.title"></h4>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="task.description"></p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full" :class="{
                                'bg-red-100 text-red-700': task.priority === 'Urgent',
                                'bg-orange-100 text-orange-700': task.priority === 'High',
                                'bg-yellow-100 text-yellow-700': task.priority === 'Medium',
                                'bg-green-100 text-green-700': task.priority === 'Low'
                            }" x-text="task.priority"></span>
                        </div>

                        <div class="flex flex-wrap gap-1">
                            <template x-for="tag in task.tags" :key="tag">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded" x-text="tag"></span>
                            </template>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t">
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                        <line x1="16" x2="16" y1="2" y2="6"></line>
                                        <line x1="8" x2="8" y1="2" y2="6"></line>
                                        <line x1="3" x2="21" y1="10" y2="10"></line>
                                    </svg>
                                    <span x-text="new Date(task.dueDate).toLocaleDateString()"></span>
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">J</div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Other tabs would follow similar pattern -->
        <div x-show="activeTab !== 'all'" class="text-center py-12 text-gray-500">
            Tasks for this tab would be filtered here
        </div>
    </div>
</div>
@endsection
