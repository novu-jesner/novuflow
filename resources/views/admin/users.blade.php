@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ searchQuery: '', roleFilter: 'all' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-1">Manage users, roles, and permissions</p>
        </div>
        <button class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="20" x2="20" y1="8" y2="14"></line>
                <line x1="23" x2="17" y1="11" y2="11"></line>
            </svg>
            Add User
        </button>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Users</h3>
            <div class="text-2xl font-bold">8</div>
            <p class="text-xs text-green-600 mt-1">+2 this month</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Admins</h3>
            <div class="text-2xl font-bold">2</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Team Leaders</h3>
            <div class="text-2xl font-bold">2</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Employees</h3>
            <div class="text-2xl font-bold">4</div>
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
                placeholder="Search users..."
                x-model="searchQuery"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
            >
        </div>
        <select x-model="roleFilter" class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent">
            <option value="all">All Roles</option>
            <option value="SuperAdmin">Super Admin</option>
            <option value="Admin">Admin</option>
            <option value="Team Leader">Team Leader</option>
            <option value="Employee">Employee</option>
        </select>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="font-semibold">All Users</h2>
            <p class="text-sm text-gray-600">Complete list of users in the system</p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">User</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Email</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Role</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Tasks Completed</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Active Tasks</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="user in [
                            { id: '1', name: 'John Doe', email: 'john@example.com', role: 'SuperAdmin', tasksCompleted: 45, activeTasks: 3 },
                            { id: '2', name: 'Sarah Smith', email: 'sarah@example.com', role: 'Admin', tasksCompleted: 38, activeTasks: 5 },
                            { id: '3', name: 'Mike Johnson', email: 'mike@example.com', role: 'Team Leader', tasksCompleted: 52, activeTasks: 4 },
                            { id: '4', name: 'Emily Davis', email: 'emily@example.com', role: 'Employee', tasksCompleted: 28, activeTasks: 6 },
                            { id: '5', name: 'James Wilson', email: 'james@example.com', role: 'Employee', tasksCompleted: 32, activeTasks: 2 },
                        ].filter(u => (roleFilter === 'all' || u.role === roleFilter) && (u.name.toLowerCase().includes(searchQuery.toLowerCase()) || u.email.toLowerCase().includes(searchQuery.toLowerCase())))" :key="user.id">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white font-semibold" x-text="user.name.charAt(0)"></div>
                                        <span class="font-medium" x-text="user.name"></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600" x-text="user.email"></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full text-white" :class="{
                                        'bg-purple-500': user.role === 'SuperAdmin',
                                        'bg-blue-500': user.role === 'Admin',
                                        'bg-green-500': user.role === 'Team Leader',
                                        'bg-gray-500': user.role === 'Employee'
                                    }" x-text="user.role"></span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-green-600 font-medium" x-text="user.tasksCompleted"></span>
                                </td>
                                <td class="py-3 px-4" x-text="user.activeTasks"></td>
                                <td class="py-3 px-4 text-right">
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="12" cy="5" r="1"></circle>
                                                <circle cx="12" cy="19" r="1"></circle>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50">
                                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                                </svg>
                                                Edit User
                                            </a>
                                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Change Role</a>
                                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Reset Password</a>
                                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M3 6h18"></path>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                                </svg>
                                                Delete User
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
