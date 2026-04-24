@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'overview' }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-gray-900">Analytics</h1>
        <p class="text-gray-600 mt-1">System-wide analytics and insights</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Total Projects</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <path d="M3 3h18v18H3z"></path>
                    <path d="M9 3v18"></path>
                    <path d="M3 9h18"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">24</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    +12% from last month
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Tasks Completed</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">342</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    +8% from last month
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Active Tasks</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">87</div>
                <p class="text-xs text-red-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 17 13.5 8.5 8.5 13.5 2 7"></polyline>
                        <polyline points="16 17 22 17 22 11"></polyline>
                    </svg>
                    -5% from last month
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Team Members</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#3f8caf]">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">48</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                    +4 new this month
                </p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b">
            <button @click="activeTab = 'overview'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'overview' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Overview</button>
            <button @click="activeTab = 'tasks'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'tasks' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Tasks</button>
            <button @click="activeTab = 'teams'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'teams' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Teams</button>
            <button @click="activeTab = 'projects'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'projects' ? 'border-[#3f8caf] text-[#3f8caf]' : 'border-transparent text-gray-600 hover:text-gray-900'">Projects</button>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="grid gap-4 lg:grid-cols-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Monthly Activity</h2>
                    <p class="text-sm text-gray-600">Tasks, projects, and users over time</p>
                </div>
                <div class="p-6">
                    <div class="h-64 flex items-end justify-between gap-2">
                        <template x-for="i in 6" :key="i">
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full flex gap-1 items-end justify-center">
                                    <div class="w-3 bg-[#3f8caf] rounded-t" :style="'height: ' + (30 + Math.random() * 50) + '%'"></div>
                                    <div class="w-3 bg-[#54acc8] rounded-t" :style="'height: ' + (20 + Math.random() * 30) + '%'"></div>
                                    <div class="w-3 bg-[#2a6a95] rounded-t" :style="'height: ' + (10 + Math.random() * 20) + '%'"></div>
                                </div>
                                <span class="text-xs text-gray-500" x-text="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'][i-1]"></span>
                            </div>
                        </template>
                    </div>
                    <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-[#3f8caf] rounded"></span> Tasks</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-[#54acc8] rounded"></span> Projects</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-[#2a6a95] rounded"></span> Users</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Task Distribution</h2>
                    <p class="text-sm text-gray-600">Current status of all tasks</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center gap-4">
                        <div class="w-40 h-40 rounded-full border-8 border-gray-200 relative" style="border-right-color: #10b981; border-bottom-color: #3b82f6; border-left-color: #6b7280;">
                            <div class="absolute inset-2 flex items-center justify-center">
                                <span class="text-2xl font-bold">100</span>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 bg-green-500 rounded"></span> Completed: 45%</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 bg-blue-500 rounded"></span> In Progress: 30%</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 bg-gray-500 rounded"></span> To Do: 20%</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 bg-red-500 rounded"></span> Blocked: 5%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other tabs placeholder -->
        <div x-show="activeTab !== 'overview'" class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
            Charts for this section would be displayed here
        </div>
    </div>
</div>
@endsection
