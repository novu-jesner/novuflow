@extends('layouts.app')

@section('title', 'Dashboard - NovuFlow')

@section('content')
<div class="min-h-screen bg-background">
    @include('partials.sidebar')
    <div class="lg:pl-64">
        @include('partials.header')
        <main class="p-6">
            @yield('dashboard-content')
        </main>
    </div>
</div>
@endsection
