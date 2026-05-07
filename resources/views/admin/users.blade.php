@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ searchQuery: '', roleFilter: 'all' }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-foreground">User Management</h1>
            <p class="text-muted-foreground mt-1">Manage users, roles, and permissions</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-primary to-secondary text-primary-foreground px-4 py-2 rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">
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
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Users</h3>
            <div class="text-2xl font-bold">{{ $totalUsers }}</div>
            <p class="text-xs text-green-600 mt-1">System users</p>
        </div>
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Admins</h3>
            <div class="text-2xl font-bold">{{ $admins }}</div>
        </div>
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Team Leaders</h3>
            <div class="text-2xl font-bold">{{ $teamLeaders }}</div>
        </div>
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Employees</h3>
            <div class="text-2xl font-bold">{{ $employees }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-4">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input
                type="text"
                placeholder="Search users..."
                x-model="searchQuery"
                class="w-full pl-10 pr-4 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
            >
        </div>
        <select x-model="roleFilter" class="w-48 px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors">
            <option value="all">All Roles</option>
            <option value="SuperAdmin">Super Admin</option>
            <option value="Admin">Admin</option>
            <option value="Team Leader">Team Leader</option>
            <option value="Employee">Employee</option>
        </select>
    </div>

    <!-- Users Table -->
    <div class="bg-card border border-border rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-border">
            <h2 class="font-semibold">All Users</h2>
            <p class="text-sm text-muted-foreground">Complete list of users in the system</p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">User</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Email</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Role</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Tasks Completed</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-muted-foreground">Active Tasks</th>
                            <th class="text-right py-3 px-4 text-sm font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $tasksCompleted = \App\Models\Task::where('assigned_to', $user->id)->where('status', 'Completed')->count();
                            $activeTasks = \App\Models\Task::where('assigned_to', $user->id)->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
                        @endphp
                        <tr class="border-b border-border hover:bg-muted/30 transition-colors" x-show="(roleFilter === 'all' || '{{ $user->role }}' === roleFilter) && ('{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(searchQuery.toLowerCase()))">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-secondary border border-border flex items-center justify-center text-white font-semibold">{{ substr($user->name, 0, 1) }}</div>
                                    <span class="font-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-muted-foreground">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs rounded-full text-white
                                    @if($user->role == 'SuperAdmin') bg-purple-600/90
                                    @elseif($user->role == 'Admin') bg-blue-600/90
                                    @elseif($user->role == 'Team Leader') bg-green-600/90
                                    @else bg-slate-600/90 @endif">{{ $user->role }}</span>
                            </td>
                            <td class="py-3 px-4">{{ $tasksCompleted }}</td>
                            <td class="py-3 px-4">{{ $activeTasks }}</td>
                            <td class="py-3 px-4 text-right flex items-center justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-primary hover:opacity-90 transition-opacity">Edit</a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-destructive hover:opacity-90 transition-opacity">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-muted-foreground">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
