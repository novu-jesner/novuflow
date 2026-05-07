<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NovuFlow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
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

<<<<<<< HEAD
            <!-- Page Content -->
            <main>
               @yield('content')
            </main>
        </div>
    </body>
=======
        async function ajaxDelete(url, opts = {}) {
            if (!confirm(opts.confirm || 'Are you sure?')) return;
            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    Alpine.store('toast').show(data.message || 'Deleted!', 'success');
                    if (opts.onSuccess) opts.onSuccess(data);
                } else {
                    Alpine.store('toast').show(data.message || 'Delete failed.', 'error');
                }
                return data;
            } catch (e) {
                Alpine.store('toast').show('Network error. Please try again.', 'error');
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Alpine.store('toast').show("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                Alpine.store('toast').show("{{ session('error') }}", 'error');
            @endif

            if (window.Alpine && Alpine.store('wellness')) {
                Alpine.store('wellness').init();
            }
        });
    </script>
</head>
<body class="min-h-screen bg-background text-foreground">
    <!-- Toast Notifications -->
    <div x-data class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none" x-show="$store.toast.items.length > 0" style="display:none;">
        <template x-for="t in $store.toast.items" :key="t.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white text-sm w-72 max-w-md"
                 :class="{ 'bg-green-600/90': t.type==='success', 'bg-red-600/90': t.type==='error', 'bg-blue-600/90': t.type==='info' }">
                <template x-if="t.type==='success'">
                    <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </template>
                <template x-if="t.type==='error'">
                    <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                </template>
                <template x-if="t.type==='info'">
                    <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>
                </template>
                <span x-text="t.message" class="flex-1"></span>
                <button @click="$store.toast.remove(t.id)" class="ml-1 hover:opacity-75 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                </button>
            </div>
        </template>
    </div>

    @yield('content')
</body>
>>>>>>> b958da1 (implement project invitation workflow and notification engine)
</html>
