<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-background text-foreground min-h-screen">
        <header class="w-full max-w-6xl mx-auto px-6 py-6 text-sm">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-2 border border-border rounded-md text-foreground hover:bg-muted/30 transition-colors"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-2 border border-transparent rounded-md text-foreground hover:border-border hover:bg-muted/20 transition-colors"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-2 border border-border rounded-md text-foreground hover:bg-muted/30 transition-colors">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        <main class="min-h-[calc(100vh-80px)] px-6 pb-10">
            <div class="max-w-4xl mx-auto">
                <div class="bg-card border border-border rounded-2xl shadow-lg p-8 md:p-12">
                    <div class="inline-flex items-center rounded-full bg-primary/10 text-primary text-xs font-semibold px-3 py-1 mb-4">
                        Welcome to NovuFlow
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-foreground mb-3">Manage projects and teams with clarity</h1>
                    <p class="text-muted-foreground mb-8 max-w-2xl">
                        NovuFlow helps your team plan work, track task progress, collaborate in real-time, and deliver consistently across every project.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-md bg-gradient-to-r from-primary to-secondary px-5 py-2.5 text-sm font-semibold text-primary-foreground hover:opacity-95 transition-opacity">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-gradient-to-r from-primary to-secondary px-5 py-2.5 text-sm font-semibold text-primary-foreground hover:opacity-95 transition-opacity">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-md border border-border bg-card px-5 py-2.5 text-sm font-semibold text-foreground hover:bg-muted/30 transition-colors">
                                    Create account
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
