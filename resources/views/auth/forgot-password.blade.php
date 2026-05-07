@extends('layouts.auth')

@section('auth-content')
@if (session('status'))
<div class="w-full bg-card text-foreground rounded-lg shadow-lg border border-border">
    <div class="space-y-1 text-center p-6">
        <div class="flex justify-center mb-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                    <rect width="3" height="3" x="7" y="7"></rect>
                    <rect width="3" height="3" x="14" y="7"></rect>
                    <rect width="3" height="3" x="7" y="14"></rect>
                    <rect width="3" height="3" x="14" y="14"></rect>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold">Check Your Email</h1>
        <p class="text-muted-foreground">{{ session('status') }}</p>
    </div>
    <div class="p-6 pt-0">
        <a href="{{ route('login') }}" class="block w-full text-center py-2 px-4 border border-border rounded-md hover:bg-muted/30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                <path d="m12 19-7-7 7-7"></path>
                <path d="M19 12H5"></path>
            </svg>
            Back to Login
        </a>
    </div>
</div>
@else
<div class="w-full bg-card text-foreground rounded-lg shadow-lg border border-border">
    <div class="space-y-1 text-center p-6">
        <div class="flex justify-center mb-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                    <rect width="3" height="3" x="7" y="7"></rect>
                    <rect width="3" height="3" x="14" y="7"></rect>
                    <rect width="3" height="3" x="7" y="14"></rect>
                    <rect width="3" height="3" x="14" y="14"></rect>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold">Forgot Password?</h1>
        <p class="text-muted-foreground">Enter your email address and we'll send you a link to reset your password</p>
    </div>
    <div class="p-6 pt-0">
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
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
            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-primary-foreground py-2 px-4 rounded-md hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-opacity">
                Send Reset Link
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-primary hover:opacity-90 flex items-center justify-center gap-1 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Back to Login
            </a>
        </div>
    </div>
</div>
@endif
@endsection
