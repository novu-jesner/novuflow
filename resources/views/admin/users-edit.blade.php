@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{
    async updateUser(e) {
        await submitForm(e.target, { 
            resetForm: false,
            onSuccess: (data) => {
                if (data.redirect) window.location.href = data.redirect;
            }
        });
    }
}">
    <div>
        <h1 class="text-3xl font-semibold text-foreground">Edit User</h1>
        <p class="text-muted-foreground mt-1">Update user information</p>
    </div>

    <div class="bg-card border border-border rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4" @submit.prevent="updateUser">
            @csrf
            @method('PUT')
            <div class="space-y-2">
                <label for="name" class="text-sm font-medium">Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="text-sm font-medium">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="role" class="text-sm font-medium">Role</label>
                <select
                    id="role"
                    name="role"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                    <option value="Employee" {{ $user->role === 'Employee' ? 'selected' : '' }}>Employee</option>
                    <option value="Team Leader" {{ $user->role === 'Team Leader' ? 'selected' : '' }}>Team Leader</option>
                    <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="SuperAdmin" {{ $user->role === 'SuperAdmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <a href="{{ route('admin.users') }}" class="px-4 py-2 border border-border rounded-md hover:bg-muted/30 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-primary-foreground rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
