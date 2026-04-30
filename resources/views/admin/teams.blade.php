@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Team Management</h1>
            <p class="text-gray-600 mt-1">Create and manage teams in your organization</p>
        </div>
        <button class="bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white px-4 py-2 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
            Create Team
        </button>
    </div>

    <!-- Stats -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Teams</h3>
            <div class="text-2xl font-bold">3</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Total Members</h3>
            <div class="text-2xl font-bold">8</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Active Projects</h3>
            <div class="text-2xl font-bold">9</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm pb-2">Avg Team Size</h3>
            <div class="text-2xl font-bold">3</div>
        </div>
    </div>

    <!-- Teams Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6 border-b">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <h3 class="font-semibold">Development Team</h3>
                        <p class="text-sm text-gray-600">Frontend and backend developers</p>
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
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Edit Team</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Manage Members</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">View Projects</a>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Delete Team</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <!-- Members -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="text-sm text-gray-600">3 Members</span>
                    </div>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">J</div>
                        <div class="w-8 h-8 rounded-full bg-green-500 border-2 border-white flex items-center justify-center text-white text-xs">S</div>
                        <div class="w-8 h-8 rounded-full bg-purple-500 border-2 border-white flex items-center justify-center text-white text-xs">E</div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex items-center justify-between pt-3 border-t">
                    <div class="text-sm">
                        <span class="text-gray-600">Projects: </span>
                        <span class="font-medium">3</span>
                    </div>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Created Jan 15, 2024</span>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors">Manage</button>
                    <button class="flex-1 px-3 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md text-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">View Details</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6 border-b">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <h3 class="font-semibold">Design Team</h3>
                        <p class="text-sm text-gray-600">UI/UX designers and creative team</p>
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
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Edit Team</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Manage Members</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">View Projects</a>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Delete Team</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <!-- Members -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="text-sm text-gray-600">2 Members</span>
                    </div>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-orange-500 border-2 border-white flex items-center justify-center text-white text-xs">M</div>
                        <div class="w-8 h-8 rounded-full bg-pink-500 border-2 border-white flex items-center justify-center text-white text-xs">J</div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex items-center justify-between pt-3 border-t">
                    <div class="text-sm">
                        <span class="text-gray-600">Projects: </span>
                        <span class="font-medium">2</span>
                    </div>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Created Jan 20, 2024</span>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors">Manage</button>
                    <button class="flex-1 px-3 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md text-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">View Details</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6 border-b">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <h3 class="font-semibold">Marketing Team</h3>
                        <p class="text-sm text-gray-600">Content creators and marketing specialists</p>
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
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Edit Team</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Manage Members</a>
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">View Projects</a>
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Delete Team</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <!-- Members -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="text-sm text-gray-600">2 Members</span>
                    </div>
                    <div class="flex -space-x-2">
                        <div class="w-8 h-8 rounded-full bg-yellow-500 border-2 border-white flex items-center justify-center text-white text-xs">L</div>
                        <div class="w-8 h-8 rounded-full bg-red-500 border-2 border-white flex items-center justify-center text-white text-xs">A</div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="flex items-center justify-between pt-3 border-t">
                    <div class="text-sm">
                        <span class="text-gray-600">Projects: </span>
                        <span class="font-medium">4</span>
                    </div>
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Created Feb 1, 2024</span>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors">Manage</button>
                    <button class="flex-1 px-3 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white rounded-md text-sm hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">View Details</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
