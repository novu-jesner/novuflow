@extends('layouts.auth')

@section('auth-content')
<div class="w-full bg-white rounded-lg shadow-lg">
    <div class="space-y-1 text-center p-6">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-12 w-auto">
        </div>
        <h1 class="text-2xl font-bold">Create an Account</h1>
        <p class="text-gray-600">Get started with NovuFlow today</p>
    </div>
    <div class="p-6 pt-0">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-2">
                <label for="role" class="text-sm font-medium">Select Your Role</label>
                <select
                    id="role"
                    name="role"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >
                    <option value="Employee">Employee</option>
                    <option value="Team Leader">Team Leader</option>
                    <option value="Admin">Admin</option>
                    <option value="SuperAdmin">Super Admin</option>
                </select>
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
            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-medium">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="••••••••"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent"
                >
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white py-2 px-4 rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                Create Account
            </button>
        </form>
        <div class="mt-4 text-center text-sm">
            Already have an account?
            <a href="{{ route('login') }}" class="text-[#3f8caf] hover:text-[#2a6a95] font-medium">
                Sign in
            </a>
        </div>
    </div>
</div>
@endsection
