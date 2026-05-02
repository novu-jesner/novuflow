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
                <div class="text-2xl font-bold">{{ $totalProjects }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">Active projects</p>
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
                <div class="text-2xl font-bold">{{ $completedTasks }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">Tasks done</p>
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
                <div class="text-2xl font-bold">{{ $activeTasks }}</div>
                <p class="text-xs text-blue-600 flex items-center gap-1 mt-1">In progress</p>
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
                <div class="text-2xl font-bold">{{ $teamMembers }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">Team members</p>
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
            @php
                $totalTaskCount = $completedTasks + $activeTasks;
                $completedPercent = $totalTaskCount > 0 ? round(($completedTasks / $totalTaskCount) * 100) : 0;
                $activePercent = $totalTaskCount > 0 ? round(($activeTasks / $totalTaskCount) * 100) : 0;
            @endphp
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">Task Overview</h2>
                    <p class="text-sm text-gray-600">Task completion statistics</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Tasks</span>
                            <span class="font-semibold">{{ $totalTaskCount }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-green-500 h-4 rounded-full" style="width: {{ $completedPercent }}%"></div>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-green-600">{{ $completedTasks }} Completed ({{ $completedPercent }}%)</span>
                            <span class="text-blue-600">{{ $activeTasks }} Active ({{ $activePercent }}%)</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="font-medium mb-3">Quick Stats</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-xs text-gray-500">Projects</div>
                                <div class="text-lg font-semibold">{{ $totalProjects }}</div>
                            </div>
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="text-xs text-gray-500">Team Members</div>
                                <div class="text-lg font-semibold">{{ $teamMembers }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="font-semibold">System Status</h2>
                    <p class="text-sm text-gray-600">Current system overview</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">All Systems Operational</div>
                                <div class="text-sm text-gray-500">Last checked: Just now</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">{{ $activeTasks }} Active Tasks</div>
                                <div class="text-sm text-gray-500">Requiring attention</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">{{ $teamMembers }} Team Members</div>
                                <div class="text-sm text-gray-500">Across all teams</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" class="bg-white rounded-lg shadow p-6" style="display: none;">
            <h3 class="font-semibold mb-4">Task Statistics</h3>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $completedTasks }}</div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $activeTasks }}</div>
                    <div class="text-sm text-gray-600">Active</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-gray-600">{{ $totalTaskCount }}</div>
                    <div class="text-sm text-gray-600">Total</div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'teams'" class="bg-white rounded-lg shadow overflow-hidden" style="display: none;">
            <div class="p-6 border-b">
                <h3 class="font-semibold">Team Overview</h3>
                <p class="text-sm text-gray-600">Complete list of all teams in the system</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 font-medium">Team & Leader</th>
                            <th class="px-6 py-3 font-medium">Projects</th>
                            <th class="px-6 py-3 font-medium">Task Completion</th>
                            <th class="px-6 py-3 font-medium text-center">Overdue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($teams as $team)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900">{{ $team->name }}</span>
                                    <span class="text-xs text-gray-500">Lead: {{ $team->leader->name ?? 'None' }} • {{ $team->members_count }} members</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $team->projects_count }} Projects
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full max-w-[100px]">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold text-[#3f8caf]">{{ $team->completion_rate }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-[#3f8caf] h-1.5 rounded-full" style="width: {{ $team->completion_rate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($team->overdue_tasks > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse">
                                        {{ $team->overdue_tasks }} Overdue
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">No teams found in the system.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Projects Tab -->
        <div x-show="activeTab === 'projects'" class="bg-white rounded-lg shadow p-6" style="display: none;">
            <h3 class="font-semibold mb-4">Project Statistics</h3>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $totalProjects }}</div>
                    <div class="text-sm text-gray-600">Total Projects</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $completedTasks }}</div>
                    <div class="text-sm text-gray-600">Tasks Completed</div>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $activeTasks }}</div>
                    <div class="text-sm text-gray-600">Tasks In Progress</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

tion
