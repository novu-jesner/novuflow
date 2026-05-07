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
            <h1 class="text-3xl font-semibold text-foreground">{{ $team ? $team->name : 'Team' }}</h1>
            <p class="text-muted-foreground mt-1">{{ $team ? ($team->description ?? 'Manage your team members and their roles') : 'Manage your team members and their roles' }}</p>
        </div>
       @if($team && auth()->user()->role !== 'Employee')
        <button @click="showInviteModal = true" class="bg-gradient-to-r from-primary to-secondary text-primary-foreground px-4 py-2 rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">
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
        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.3-4.3"></path>
        </svg>
        <input
            type="text"
            placeholder="Search team members..."
            x-model="searchQuery"
            class="w-full pl-10 pr-4 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
        >
    </div>

    <!-- Team Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Members</h3>
            <div class="text-2xl font-bold">{{ $totalMembers }}</div>
        </div>
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Active Tasks</h3>
            <div class="text-2xl font-bold">{{ $activeTasks }}</div>
        </div>
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Completed Tasks</h3>
            <div class="text-2xl font-bold">{{ $completedTasks }}</div>
        </div>
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Avg. Completion Rate</h3>
            <div class="text-2xl font-bold">{{ $avgCompletion }}%</div>
        </div>
    </div>

    <!-- Team Members List -->
    <div class="bg-card border border-border rounded-lg shadow">
        <div class="p-6 border-b border-border">
            <h2 class="font-semibold">Team Members</h2>
            <p class="text-sm text-muted-foreground">All members in your organization</p>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($members as $member)
                  
                @php
                    $tasksCompleted = \App\Models\Task::where('assigned_to', $member->id)->where('status', 'Completed')->count();
                    $activeTasks = \App\Models\Task::where('assigned_to', $member->id)->whereIn('status', ['To Do', 'In Progress', 'Review'])->count();
                @endphp
                <div class="flex items-center justify-between p-4 border border-border rounded-lg hover:bg-muted/20 transition-colors" 
                     x-data="{ show: true }" 
                     x-show="show && ('{{ strtolower($member->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($member->email) }}'.includes(searchQuery.toLowerCase()))">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary to-secondary border-2 border-card flex items-center justify-center text-white font-semibold">{{ substr($member->name, 0, 1) }}</div>
                        <div>
                            <div class="font-medium">{{ $member->name }}</div>
                            <div class="text-sm text-muted-foreground flex items-center gap-1">
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
                            @if($member->role == 'SuperAdmin') bg-purple-600/90
                            @elseif($member->role == 'Admin') bg-blue-600/90
                            @elseif($member->role == 'Team Leader') bg-green-600/90
                            @else bg-slate-600/90 @endif">{{ $member->role }}</span>
                    <div class="text-right">
                        <div class="text-sm font-medium text-green-600">{{ $tasksCompleted }} completed</div>
                        <div class="text-xs text-muted-foreground">{{ $activeTasks }} active</div>
                    </div>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 hover:bg-muted/40 rounded-md transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-popover rounded-lg shadow-lg border border-border z-50 overflow-hidden">
                                    <a href="{{ route('team.member.profile', $member->id) }}" class="block px-4 py-2 text-sm hover:bg-muted/30 transition-colors">View Profile</a>
                                    @if(auth()->user()->role === 'SuperAdmin')
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="block px-4 py-2 text-sm hover:bg-muted/30 transition-colors">Change Role</a>
                                    @endif
                                    @if(
    auth()->user()->role !== 'Employee' &&
    !(
        auth()->user()->role === 'Team Leader' &&
        in_array($member->role, ['Admin', 'SuperAdmin'])
    )
)

<button type="button"
  @click="ajaxDelete('{{ route('team.member.remove', $member->id) }}', {
      onSuccess: () => { show = false; open = false; }
  })"
  class="block w-full text-left px-4 py-2 text-sm text-destructive hover:bg-muted/30 transition-colors">
  Remove Member
</button>
@endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-muted-foreground">No team members found</div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Invite Member Modal -->
    <div x-show="showInviteModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showInviteModal" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showInviteModal" x-transition @click.away="showInviteModal = false" class="relative transform overflow-hidden rounded-lg bg-card border border-border text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-semibold leading-6 text-foreground mb-4" id="modal-title">Invite Member to Team</h3>
                                <form action="{{ route('team.invite') }}" method="POST" @submit.prevent="inviteMember">
                                    @csrf
                                    <input type="hidden" name="team_id" value="{{ $team->id ?? '' }}">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-foreground">Select User</label>
                                            <select name="email" id="email" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base bg-surface border border-input focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring sm:text-sm rounded-md">
                                                <option value="">Choose a user to invite</option>
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-reverse sm:grid-cols-2 sm:gap-3">
                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-gradient-to-r from-primary to-secondary px-3 py-2 text-sm font-semibold text-primary-foreground shadow-sm hover:opacity-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ring transition-opacity">Invite Member</button>
                                        <button type="button" @click="showInviteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-card px-3 py-2 text-sm font-semibold text-foreground shadow-sm border border-border hover:bg-muted/30 sm:mt-0 transition-colors">Cancel</button>
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
