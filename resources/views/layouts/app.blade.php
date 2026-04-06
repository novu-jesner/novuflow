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
           <aside class="w-64 bg-white shadow p-4">
    <h2 class="text-xl font-bold mb-4">Menu</h2>

    <ul class="space-y-2">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('dashboard') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Dashboard
            </a>
        </li>

        <!-- Boards -->
        <li>
            <a href="{{ route('boards.index') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Boards
            </a>
        </li>

        <!-- Projects -->
        <li>
            <a href="{{ route('projects.index') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Projects
            </a>
        </li>

        <!-- Teams -->
        <li>
            <a href="{{ route('teams.index') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Teams
            </a>
        </li>
    </ul>


</aside>
            <!-- Main Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>