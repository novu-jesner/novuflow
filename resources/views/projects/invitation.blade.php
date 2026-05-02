@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b bg-gray-50/50">
            <h1 class="text-xl font-semibold text-gray-900">Project Invitation</h1>
            <p class="text-sm text-gray-600 mt-1">You have been invited to collaborate on this project.</p>
        </div>

        <div class="p-8 space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center text-white text-2xl font-bold shadow-sm">
                    {{ substr($project->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h2>
                    <p class="text-gray-500">Created by <span class="font-medium text-gray-900">{{ $project->creator->name }}</span></p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Description</label>
                    <div class="text-gray-700 bg-gray-50 rounded-lg p-4 text-sm leading-relaxed border border-gray-100">
                        {{ $project->description ?? 'No description provided.' }}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 py-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Due Date</label>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <path d="M3 10h18"></path>
                            </svg>
                            <span class="font-medium">{{ $project->due_date->format('F d, Y') }}</span>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest">Team Size</label>
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span class="font-medium">{{ $project->members->count() }} Members</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100">
                <form method="POST" action="{{ route('projects.invite.accept', $project->id) }}">
                    @csrf
                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white font-bold rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-all shadow-md text-lg">
                        Accept Invitation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
