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
        <h1 class="text-3xl font-semibold text-gray-900">Edit User</h1>
        <p class="text-gray-600 mt-1">Update user information</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                <a href="{{ route('admin.users') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
