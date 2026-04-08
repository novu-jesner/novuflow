<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <div class="flex h-screen bg-gray-100">
           <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
                <div class="h-full overflow-y-auto px-3 py-4">
                    <h2 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Main Menu</h2>
                    <ul class="space-y-1 mb-8">
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Dashboard
                            </a>
                        </li>
                    </ul>

                    <div x-data="{ openAddProject: false }">
                        <div class="flex items-center justify-between px-2 mb-2">
                            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Projects   </h2>
                            <button @click="openAddProject = !openAddProject" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>

                        <!-- Add Project Inline Form -->
                        <div x-show="openAddProject" style="display: none;" class="px-2 mb-4">
                            <form action="{{ route('projects.store') }}" method="POST" class="flex flex-col space-y-2">
                                @csrf
                                <input type="text" name="name" placeholder="Project Name..." required class="text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full px-2 py-1.5">
                                <div class="flex justify-end space-x-2">
                                    <button type="button" @click="openAddProject = false" class="text-xs px-2 py-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Cancel</button>
                                    <button type="submit" class="text-xs px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">Save</button>
                                </div>
                            </form>
                        </div>

                        <ul class="space-y-1">
                            @foreach($sidebarProjects as $project)
                            <li>
                                <a href="{{ route('projects.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 truncate">
                                    <span class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></span>
                                    {{ $project->name }}
                                </a>
                            </li>
                            @endforeach
                            
                            @if($sidebarProjects->isEmpty())
                            <li class="px-2 py-1 text-sm text-gray-400 dark:text-gray-500 italic">No projects yet.</li>
                            @endif
                        </ul>
                    </div>
                </div></aside>
            <!-- Main Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>