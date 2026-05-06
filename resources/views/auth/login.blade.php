@extends('layouts.auth')

@section('auth-content')
<div class="w-full bg-white rounded-lg shadow-lg">
    <div class="space-y-1 text-center p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-12 w-auto">
        </div>
        <h1 class="text-2xl font-bold">Welcome to NovuFlow</h1>
        <p class="text-gray-600">Sign in to your account to continue</p>
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >
                @error('password')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route('password.request') }}" class="text-sm text-[#3f8caf] hover:text-[#2a6a95]">
                    Forgot password?
                </a>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white py-2 px-4 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                Sign in
            </button>
        </form>
        <div class="mt-4 text-center text-sm">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-[#3f8caf] hover:text-[#2a6a95] font-medium">
                Sign up
            </a>
        </div>
        <div class="mt-6 p-3 bg-blue-50 rounded-lg">
            <p class="text-xs text-gray-600 font-medium">Demo Accounts (password: password):</p>
            <p class="text-xs text-gray-500 mt-1">
                admin@example.com - SuperAdmin
            </p>
        </div>
    </div>
</div>
@endsection
