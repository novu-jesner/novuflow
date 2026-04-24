@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#aecfdd] via-[#54acc8] to-[#3f8caf] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        @yield('auth-content')
    </div>
</div>
@endsection
