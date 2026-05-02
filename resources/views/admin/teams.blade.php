@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Team Management</h1>
            <p class="text-gray-600 mt-1">Create and manage teams in your organization</p>
        </div>
        <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
            Create Team
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Teams</h3>
            <div class="text-2xl font-bold text-gray-900">{{ $totalTeams }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Total Members</h3>
            <div class="text-2xl font-bold text-gray-900">{{ $totalMembers }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Active Projects</h3>
            <div class="text-2xl font-bold text-gray-900">{{ $activeProjects }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2">Avg Team Size</h3>
            <div class="text-2xl font-bold text-gray-900">{{ $avgTeamSize }}</div>
        </div>
    </div>

    <!-- Teams Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($teams as $team)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow" x-data="{ show: true }" x-show="show">
            <!-- Card Header -->
            <div class="p-4 md:p-6 border-b border-gray-200">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 truncate">{{ $team->name }}</h3>
                        <p class="text-sm text-gray-600 truncate">{{ $team->description ?? 'No description' }}</p>
                    </div>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-md flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="1"></circle>
                                <circle cx="12" cy="5" r="1"></circle>
                                <circle cx="12" cy="19" r="1"></circle>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 top-8 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50" style="display: none;">
                            <a href="{{ route('admin.teams.edit', $team->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Edit Team</a>
                            <a href="{{ route('team.index', $team->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">View Projects</a>
                            <button type="button" @click="ajaxDelete('{{ route('admin.teams.destroy', $team->id) }}', { onSuccess: () => { show = false; open = false; } })" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Delete Team</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-4 md:p-6 space-y-4">
                <!-- Members Section -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 flex-shrink-0">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="text-sm text-gray-600">{{ $team->members->count() }} Member{{ $team->members->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    @if($team->members->count() > 0)
                    <div class="flex -space-x-2">
                        @foreach($team->members->take(4) as $member)
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs font-medium" title="{{ $member->name }}">
                            {{ substr($member->name, 0, 1) }}
                        </div>
                        @endforeach
                        @if($team->members->count() > 4)
                        <div class="w-8 h-8 rounded-full bg-gray-400 border-2 border-white flex items-center justify-center text-white text-xs font-medium">
                            +{{ $team->members->count() - 4 }}
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-sm text-gray-400">No members assigned</div>
                    @endif
                </div>

                <!-- Stats Section -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                    <div class="text-sm">
                        <span class="text-gray-600">Projects: </span>
                        <span class="font-medium text-gray-900">{{ $team->projects->count() }}</span>
                    </div>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                        {{ $team->created_at->format('M d, Y') }}
                    </span>
                </div>

                <!-- Actions Section -->
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('admin.teams.edit', $team->id) }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm text-center hover:bg-gray-50 transition-colors">
                        Manage
                    </a>
                    <a href="{{ route('team.index', $team->id) }}" class="flex-1 px-3 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md text-sm text-center hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No teams found</h3>
            <p class="text-gray-600 mb-4">Get started by creating your first team</p>
            <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Create Team
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
