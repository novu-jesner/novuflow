@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-accent via-secondary to-primary dark:from-background dark:via-popover dark:to-surface flex items-center justify-center p-4 transition-colors">
    <div class="w-full max-w-md">
        @yield('auth-content')
    </div>
</div>
@endsection
