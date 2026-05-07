@extends('layouts.auth')

@section('auth-content')
<div class="w-full bg-card text-foreground rounded-lg shadow-lg border border-border">
    <div class="space-y-1 text-center p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-12 w-auto">
        </div>
        <h1 class="text-2xl font-bold">Create an Account</h1>
        <p class="text-muted-foreground">Get started with NovuFlow today</p>
    </div>
    <div class="p-6 pt-0">
        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf
            <div class="space-y-2">
                <label for="name" class="text-sm font-medium">Full Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    placeholder="John Doe"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
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
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <input type="hidden" name="role" value="Employee">
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
            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-medium">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="••••••••"
                    required
                    class="w-full px-3 py-2 bg-surface border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-colors"
                >
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-primary-foreground py-2 px-4 rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">
                Create Account
            </button>
        </form>
        <div class="mt-4 text-center text-sm">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary hover:opacity-90 font-medium transition-opacity">
                Sign in
            </a>
        </div>
    </div>
</div>
@endsection
