<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <script>
        (function () {
            try {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Scrollbar for a premium look */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* gray-300 */
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569; /* gray-600 */
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8; /* gray-400 */
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #64748b; /* gray-500 */
        }
        /* Alpine.js cloak to hide uninitialized components */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-900 h-screen flex flex-col overflow-hidden">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 shrink-0">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Main Wrapper (h-screen ensures it doesn't break out) -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 shrink-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
            <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                
                <!-- Main Menu Section -->
                <div class="mb-8">
                    <h2 class="px-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Main Menu</h2>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('members.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                                <svg class="w-5 h-5 mr-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Members
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Projects Section -->
                <div x-data="{ openAddProject: false }">
                    <div class="flex items-center justify-between px-3 mb-2">
                        <h2 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Projects</h2>
                        <button @click="openAddProject = !openAddProject" class="p-1 rounded-md text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Add Project">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>

                    <!-- Add Project Inline Form -->
                    <div x-show="openAddProject" x-cloak x-transition.opacity class="mb-4 px-3">
                        <form action="{{ route('projects.store') }}" method="POST" class="flex flex-col gap-2">
                            @csrf
                            <input type="text" name="name" placeholder="Project Name..." required class="text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 placeholder-gray-400 dark:placeholder-gray-500 transition-colors">
                            <div class="flex justify-end gap-2">
                                <button type="button" @click="openAddProject = false" class="text-xs px-3 py-1.5 font-medium text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Cancel</button>
                                <button type="submit" class="text-xs px-3 py-1.5 font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm transition-colors">Save</button>
                            </div>
                        </form>
                    </div>

                    <!-- Projects List -->
                    <ul class="space-y-1">
                        @foreach($sidebarProjects as $project)
                        <li x-data="{ editing: false }" class="group flex items-center justify-between px-3 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors">
                            <!-- Display mode -->
                            <a x-show="!editing" href="{{ route('projects.show', $project) }}" class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 truncate flex-grow">
                                <span class="w-2 h-2 bg-indigo-500 dark:bg-indigo-400 rounded-full mr-3 shrink-0 shadow-sm opacity-70 group-hover:opacity-100 transition-opacity"></span>
                                <span class="truncate">{{ $project->name }}</span>
                            </a>

                            <!-- Inline edit form -->
                            <form x-show="editing" x-cloak action="{{ route('projects.update', $project) }}" method="POST" class="flex items-center gap-2 flex-grow" @click.away="editing = false">
                                @csrf
                                @method('PATCH')
                                <input x-ref="projInput{{ $project->id }}" type="text" name="name" value="{{ $project->name }}" required class="text-sm w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <div class="flex items-center gap-1">
                                    <button type="submit" class="text-xs px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Save</button>
                                    <button type="button" @click.prevent="editing = false" class="text-xs px-2 py-1 text-gray-500 rounded-md">Cancel</button>
                                </div>
                            </form>

                            <!-- Edit and delete controls -->
                            <div class="ml-2 flex items-center gap-1">
                                <button x-show="!editing" x-cloak type="button" @click.stop="editing = true; $nextTick(() => { $refs['projInput' + {{ $project->id }}].focus() })" class="ml-0 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity p-1 rounded text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700/50" title="Edit Project Name">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6m-6 4h6M5 7h.01M5 11h.01M5 15h.01M5 19h.01"></path></svg>
                                </button>

                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="ml-0 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors focus:outline-none" title="Delete Project">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                        @endforeach
                        
                        @if($sidebarProjects->isEmpty())
                        <li class="px-3 py-4 mt-2 text-sm text-gray-400 dark:text-gray-500 italic text-center border border-dashed border-gray-200 dark:border-gray-700 rounded-lg">No projects yet.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-auto bg-gray-50 dark:bg-gray-900 custom-scrollbar p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>