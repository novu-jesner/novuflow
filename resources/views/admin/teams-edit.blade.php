@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{
    async updateTeam(e) {
        await submitForm(e.target, { resetForm: false });
    }
}">
    <div>
        <h1 class="text-3xl font-semibold text-gray-900">Edit Team</h1>
        <p class="text-gray-600 mt-1">Update team information</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >
                    <option value="">Select a leader</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('leader_id', $team->leader_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
                @error('leader_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <a href="{{ route('admin.teams') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">Update Team</button>
            </div>
        </form>
    </div>
</div>
@endsection
