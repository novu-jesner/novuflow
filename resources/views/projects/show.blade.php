@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ url('/dashboard/projects') }}" class="p-2 hover:bg-gray-100 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-semibold text-gray-900">Website Redesign</h1>
                    <span class="px-2 py-1 text-xs rounded-full text-white bg-blue-500">In Progress</span>
                </div>
                <p class="text-gray-600 mt-1">Complete overhaul of company website with new branding</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.74v-.47a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.39a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                Settings
            </button>
            <a href="{{ route('kanban.board', $project->id) }}" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                View Board
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Progress</h3>
            <div class="text-2xl font-bold">75%</div>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                <div class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] h-2 rounded-full" style="width: 75%"></div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Team Members</h3>
            <div class="text-2xl font-bold">5</div>
            <p class="text-xs text-gray-500 mt-1">Active collaborators</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Start Date</h3>
            <div class="text-lg font-medium">Jan 1, 2024</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Due Date</h3>
            <div class="text-lg font-medium">Dec 31, 2024</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b">
            <button @click="activeTab = 'overview'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'overview' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Overview</button>
            <button @click="activeTab = 'tasks'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'tasks' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Tasks</button>
            <button @click="activeTab = 'team'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'team' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Team</button>
            <button @click="activeTab = 'activity'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'activity' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Activity</button>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Project Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <div>
                                <div class="text-sm font-medium">Start Date</div>
                                <div class="text-sm text-gray-600">January 1, 2024</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                            <div>
                                <div class="text-sm font-medium">Due Date</div>
                                <div class="text-sm text-gray-600">December 31, 2024</div>
                            </div>
                        </div>
                        <div class="pt-2">
                            <div class="text-sm text-gray-600">242 days remaining</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Quick Stats</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Tasks</span>
                            <span class="font-medium">42</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Completed</span>
                            <span class="font-medium text-green-600">24</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">In Progress</span>
                            <span class="font-medium text-blue-600">12</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">To Do</span>
                            <span class="font-medium text-gray-600">6</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="font-semibold">All Tasks</h2>
                <p class="text-sm text-gray-600">Tasks for this project</p>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    <template x-for="task in [
                        { id: '1', title: 'Design homepage mockup', assignedTo: 'John Doe', priority: 'Urgent' },
                        { id: '2', title: 'Implement authentication', assignedTo: 'Sarah Smith', priority: 'High' },
                        { id: '3', title: 'Write API documentation', assignedTo: 'Mike Johnson', priority: 'Medium' },
                        { id: '4', title: 'Setup CI/CD pipeline', assignedTo: 'Alice Brown', priority: 'Low' },
                    ]" :key="task.id">
                        <div class="flex items-center justify-between p-3 border rounded-md hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full" :class="{
                                    'bg-red-500': task.priority === 'Urgent',
                                    'bg-orange-500': task.priority === 'High',
                                    'bg-yellow-500': task.priority === 'Medium',
                                    'bg-green-500': task.priority === 'Low'
                                }"></div>
                                <div>
                                    <div class="font-medium" x-text="task.title"></div>
                                    <div class="text-sm text-gray-500" x-text="task.assignedTo"></div>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full" :class="{
                                'bg-red-100 text-red-700': task.priority === 'Urgent',
                                'bg-gray-100 text-gray-700': task.priority !== 'Urgent'
                            }" x-text="task.priority"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Team Tab -->
        <div x-show="activeTab === 'team'" class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="font-semibold">Team Members</h2>
                <p class="text-sm text-gray-600">People working on this project</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <template x-for="member in [
                        { id: '1', name: 'John Doe', role: 'Developer', tasksCompleted: 12 },
                        { id: '2', name: 'Sarah Smith', role: 'Designer', tasksCompleted: 8 },
                        { id: '3', name: 'Mike Johnson', role: 'Developer', tasksCompleted: 15 },
                        { id: '4', name: 'Alice Brown', role: 'Manager', tasksCompleted: 10 },
                    ]" :key="member.id">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white" x-text="member.name.charAt(0)"></div>
                                <div>
                                    <div class="font-medium" x-text="member.name"></div>
                                    <div class="text-sm text-gray-500" x-text="member.role"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium" x-text="member.tasksCompleted + ' tasks'"></div>
                                <div class="text-xs text-gray-500">completed</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Activity Tab -->
        <div x-show="activeTab === 'activity'" class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="font-semibold">Recent Activity</h2>
                <p class="text-sm text-gray-600">Latest updates from the team</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <template x-for="activity in [
                        { id: '1', userName: 'John Doe', description: 'completed task', taskTitle: 'Update homepage', time: '2 hours ago' },
                        { id: '2', userName: 'Sarah Smith', description: 'commented on', taskTitle: 'API Integration', time: '4 hours ago' },
                        { id: '3', userName: 'Mike Johnson', description: 'created new project', taskTitle: 'Q1 Marketing', time: 'Yesterday' },
                    ]" :key="activity.id">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-sm" x-text="activity.userName.charAt(0)"></div>
                            <div class="flex-1">
                                <p class="text-sm">
                                    <span class="font-medium" x-text="activity.userName"></span>
                                    <span x-text="' ' + activity.description + ' '"></span>
                                    <span class="text-[#3f8caf]" x-text="'\"' + activity.taskTitle + '\"'"></span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1" x-text="activity.time"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
