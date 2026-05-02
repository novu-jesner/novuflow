@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ 
    searchQuery: '', 
    showInviteModal: false,
    async inviteMember(e) {
        await submitForm(e.target, {
            onSuccess: (data) => {
                this.showInviteModal = false;
                window.location.reload();
            }
        });
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">{{ $team ? $team->name : 'Team' }}</h1>
            <p class="text-gray-600 mt-1">{{ $team ? ($team->description ?? 'Manage your team members and their roles') : 'Manage your team members and their roles' }}</p>
        </div>
        @if($team)
        <button @click="showInviteModal = true" class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="20" x2="20" y1="8" y2="14"></line>
                <line x1="23" x2="17" y1="11" y2="11"></line>
            </svg>
            Invite Member
        </button>
        @endif
    </div>

    <!-- Search -->
    <div class="relative max-w-md">
        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
        </svg>
        <input
            type="text"
            placeholder="Search team members..."
            x-model="searchQuery"
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
        >
    </div>

    <!-- Team Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Members</h3>
            <div class="text-2xl font-bold">{{ $totalMembers }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Active Tasks</h3>
            <div class="text-2xl font-bold">{{ $activeTasks }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Completed Tasks</h3>
            <div class="text-2xl font-bold">{{ $completedTasks }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Avg. Completion Rate</h3>
            <div class="text-2xl font-bold">{{ $avgCompletion }}%</div>
        </div>
    </div>

    <!-- Team Members List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="font-semibold">Team Members</h2>
            <p class="text-sm text-gray-600">All members in your organization</p>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($members as $member)
                @php
                    $tasksCompleted = \App\Models\Task::where('assigned_to', $member->id)->where('status', 'Completed')->count();
                    $activeTasks = \App\Models\Task::where('assigned_to', $member->id)->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
                @endphp
                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors" 
                     x-data="{ show: true }" 
                     x-show="show && ('{{ strtolower($member->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($member->email) }}'.includes(searchQuery.toLowerCase()))">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white font-semibold">{{ substr($member->name, 0, 1) }}</div>
                        <div>
                            <div class="font-medium">{{ $member->name }}</div>
                            <div class="text-sm text-gray-500 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                                <span>{{ $member->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <span class="px-2 py-1 text-xs rounded-full text-white
                            @if($member->role == 'SuperAdmin') bg-purple-500
                            @elseif($member->role == 'Admin') bg-blue-500
                            @elseif($member->role == 'Team Leader') bg-green-500
                            @else bg-gray-500 @endif">{{ $member->role }}</span>
                    <div class="text-right">
                        <div class="text-sm font-medium text-green-600">{{ $tasksCompleted }} completed</div>
                        <div class="text-xs text-gray-500">{{ $activeTasks }} active</div>
                    </div>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 hover:bg-gray-100 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50">
                                    <a href="{{ route('team.member.profile', $member->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">View Profile</a>
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50">Change Role</a>
                                    <button type="button" @click="ajaxDelete('{{ route('team.member.remove', $member->id) }}', { onSuccess: () => { show = false; open = false; } })" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Remove Member</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">No team members found</div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Invite Member Modal -->
    <div x-show="showInviteModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showInviteModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showInviteModal" x-transition @click.away="showInviteModal = false" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-4" id="modal-title">Invite Member to Team</h3>
                                <form action="{{ route('team.invite') }}" method="POST" @submit.prevent="inviteMember">
                                    @csrf
                                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700">Select User</label>
                                            <select name="email" id="email" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-[#54acc8] focus:border-[#54acc8] sm:text-sm rounded-md">
                                                <option value="">Choose a user to invite</option>
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-reverse sm:grid-cols-2 sm:gap-3">
                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-gradient-to-r from-[#3f8caf] to-[#54acc8] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:from-[#2a6a95] hover:to-[#3f8caf] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3f8caf]">Invite Member</button>
                                        <button type="button" @click="showInviteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
