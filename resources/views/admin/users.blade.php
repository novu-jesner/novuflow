@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ searchQuery: '', roleFilter: 'all' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-1">Manage users, roles, and permissions</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="20" x2="20" y1="8" y2="14"></line>
                <line x1="23" x2="17" y1="11" y2="11"></line>
            </svg>
            Add User
        </a>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Users</h3>
            <div class="text-2xl font-bold">{{ $totalUsers }}</div>
            <p class="text-xs text-green-600 mt-1">System users</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Admins</h3>
            <div class="text-2xl font-bold">{{ $admins }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Team Leaders</h3>
            <div class="text-2xl font-bold">{{ $teamLeaders }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Employees</h3>
            <div class="text-2xl font-bold">{{ $employees }}</div>
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
                        @forelse($users as $user)
                        @php
                            $tasksCompleted = \App\Models\Task::where('assigned_to', $user->id)->where('status', 'Completed')->count();
                            $activeTasks = \App\Models\Task::where('assigned_to', $user->id)->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
                        @endphp
                        <tr class="border-b hover:bg-gray-50" x-show="(roleFilter === 'all' || '{{ $user->role }}' === roleFilter) && ('{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(searchQuery.toLowerCase()))">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white font-semibold">{{ substr($user->name, 0, 1) }}</div>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full text-white
                                    @if($user->role == 'SuperAdmin') bg-purple-500
                                    @elseif($user->role == 'Admin') bg-blue-500
                                    @elseif($user->role == 'Team Leader') bg-green-500
                                    @else bg-gray-500 @endif">{{ $user->role }}</span>
                            </td>
                            <td class="py-3 px-4">{{ $tasksCompleted }}</td>
                            <td class="py-3 px-4">{{ $activeTasks }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-[#3f8caf] hover:text-[#2a6a95]">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
