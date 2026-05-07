@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{
    async updateTeam(e) {
        await submitForm(e.target, { 
            resetForm: false,
            onSuccess: (data) => {
                if (data.redirect) window.location.href = data.redirect;
            }
        });
    }
}">
    <div>
        <h1 class="text-3xl font-semibold text-foreground">Edit Team</h1>
        <p class="text-muted-foreground mt-1">Update team information</p>
    </div>

    <div class="bg-card border border-border rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.teams.update', $team->id) }}" class="space-y-4" @submit.prevent="updateTeam">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label for="name" class="text-sm font-medium">Team Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $team->name) }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="description" class="text-sm font-medium">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >{{ old('description', $team->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="leader_id" class="text-sm font-medium">Team Leader</label>
                <select
                    id="leader_id"
                    name="leader_id"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                    <option value="">Select a leader</option>
                    @foreach($leaders as $user)
                    <option value="{{ $user->id }}" {{ old('leader_id', $team->leader_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2" x-data="{ 
                search: '', 
                showDropdown: false,
                allUsers: {{ $allUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role])->toJson() }},
                selected: {{ $team->members->pluck('id')->toJson() }},
                get filteredUsers() {
                    return this.allUsers.filter(u => 
                        !this.selected.includes(u.id) && 
                        (u.name.toLowerCase().includes(this.search.toLowerCase()) || 
                         u.email.toLowerCase().includes(this.search.toLowerCase()))
                    );
                },
                get selectedUsers() {
                    return this.allUsers.filter(u => this.selected.includes(u.id));
                }
            }">
                <label class="text-sm font-medium">Team Members</label>
                
                <!-- Selected Tags -->
                <div class="flex flex-wrap gap-2 mb-2">
                    <template x-for="user in selectedUsers" :key="user.id">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-foreground text-sm font-medium border border-border">
                            <span x-text="user.name"></span>
                            <button type="button" @click="selected = selected.filter(i => i !== user.id)" class="hover:opacity-80 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                            </button>
                            <input type="hidden" name="member_ids[]" :value="user.id">
                        </span>
                    </template>
                </div>

                <!-- Search Input -->
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="search" 
                        @focus="showDropdown = true"
                        @click.away="showDropdown = false"
                        placeholder="Search users to add..." 
                        class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors text-sm"
                    >
                    
                    <!-- Dropdown -->
                    <div 
                        x-show="showDropdown && filteredUsers.length > 0" 
                        class="absolute z-10 w-full mt-1 bg-popover border border-border rounded-md shadow-lg max-h-60 overflow-y-auto"
                        style="display: none;"
                    >
                        <template x-for="user in filteredUsers" :key="user.id">
                            <button 
                                type="button" 
                                @click="selected.push(user.id); search = '';"
                                class="w-full text-left px-4 py-2 hover:bg-muted/30 flex flex-col transition-colors border-b last:border-0 border-border"
                            >
                                <span class="text-sm font-medium text-foreground" x-text="user.name"></span>
                                <span class="text-xs text-muted-foreground" x-text="`${user.role} • ${user.email}`"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <p class="text-xs text-muted-foreground mt-1">Search and select users to add them as members.</p>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('admin.teams') }}" class="px-4 py-2 border border-border rounded-md hover:bg-muted/30 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-primary-foreground rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">Update Team</button>
            </div>
        </form>
    </div>
</div>
@endsection
