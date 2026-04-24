@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Website Redesign</h1>
            <p class="text-gray-600 mt-1">Project board for website redesign project</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.39a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Board Settings
            </button>
            <button class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Add Task
            </button>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 overflow-x-auto">
        <!-- To Do Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">To Do</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">2</span>
            </div>
            <div class="space-y-3">
                <div class="bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Design homepage mockups</h4>
                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded-full">High</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Create initial design concepts for the new homepage</p>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Design</span>
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded">UI/UX</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-6 h-6 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">M</div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Apr 5</span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                                </svg>
                                3
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Write blog post about new features</h4>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Low</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Create engaging content highlighting the latest product updates</p>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Content</span>
                        <span class="px-2 py-1 bg-pink-100 text-pink-700 text-xs rounded">Marketing</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-6 h-6 rounded-full bg-purple-500 border-2 border-white flex items-center justify-center text-white text-xs">E</div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Apr 8</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">In Progress</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">2</span>
            </div>
            <div class="space-y-3">
                <div class="bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Implement responsive navigation</h4>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Medium</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Build mobile-friendly navigation menu with hamburger icon</p>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">Development</span>
                        <span class="px-2 py-1 bg-cyan-100 text-cyan-700 text-xs rounded">Frontend</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">S</div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Apr 10</span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                                </svg>
                                1
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900">User testing sessions</h4>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Medium</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Conduct usability testing with 10 participants</p>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded">Research</span>
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded">UX</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-6 h-6 rounded-full bg-pink-500 border-2 border-white flex items-center justify-center text-white text-xs">L</div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Apr 12</span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                                </svg>
                                5
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">Review</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">1</span>
            </div>
            <div class="space-y-3">
                <div class="bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900">Set up CI/CD pipeline</h4>
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Urgent</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Configure automated testing and deployment workflows</p>
                    <div class="flex flex-wrap gap-1 mb-3">
                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded">DevOps</span>
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Infrastructure</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-6 h-6 rounded-full bg-orange-500 border-2 border-white flex items-center justify-center text-white text-xs">J</div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>Apr 3</span>
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                                </svg>
                                2
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Column -->
        <div class="bg-gray-100 rounded-lg p-4 min-w-[300px]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-700">Completed</h3>
                <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">0</span>
            </div>
            <div class="space-y-3">
                <div class="text-center py-8 text-gray-400 text-sm">
                    No completed tasks yet
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
