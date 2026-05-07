@extends('layouts.auth')

@section('auth-content')
<div class="w-full bg-card text-foreground rounded-lg shadow-lg border border-border">
    <div class="space-y-1 text-center p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-12 w-auto">
        </div>
        <h1 class="text-2xl font-bold">Welcome to NovuFlow</h1>
        <p class="text-muted-foreground">Sign in to your account to continue</p>
    </div>
    <div class="p-6 pt-0">
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route('password.request') }}" class="text-sm text-primary hover:opacity-90 transition-opacity">
                    Forgot password?
                </a>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-primary-foreground py-2 px-4 rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">
                Sign in
            </button>
        </form>
        <div class="mt-4 text-center text-sm">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary hover:opacity-90 font-medium transition-opacity">
                Sign up
            </a>
        </div>
        <div class="mt-6 p-3 bg-primary/10 border border-border rounded-lg">
            <p class="text-xs text-muted-foreground font-medium">Demo Accounts (password: password):</p>
            <p class="text-xs text-muted-foreground/80 mt-1">
                admin@example.com - SuperAdmin
            </p>
        </div>
    </div>
</div>
@endsection
