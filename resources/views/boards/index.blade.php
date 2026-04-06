@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Boards</h1>

<div class="grid grid-cols-4 gap-4">

    <!-- TO DO -->
    <div class="bg-gray-100 p-3 rounded">
        <h2 class="font-bold mb-2">To Do</h2>
        @foreach($tasks['todo'] ?? [] as $task)
            <div class="bg-white p-2 mb-2 rounded shadow">
                {{ $task->title }}
            </div>
        @endforeach
    </div>

    <!-- IN PROGRESS -->
    <div class="bg-gray-100 p-3 rounded">
        <h2 class="font-bold mb-2">In Progress</h2>
        @foreach($tasks['in_progress'] ?? [] as $task)
            <div class="bg-white p-2 mb-2 rounded shadow">
                {{ $task->title }}
            </div>
        @endforeach
    </div>

    <!-- REVIEW -->
    <div class="bg-gray-100 p-3 rounded">
        <h2 class="font-bold mb-2">Review</h2>
        @foreach($tasks['review'] ?? [] as $task)
            <div class="bg-white p-2 mb-2 rounded shadow">
                {{ $task->title }}
            </div>
        @endforeach
    </div>

    <!-- COMPLETED -->
    <div class="bg-gray-100 p-3 rounded">
        <h2 class="font-bold mb-2">Completed</h2>
        @foreach($tasks['completed'] ?? [] as $task)
            <div class="bg-white p-2 mb-2 rounded shadow">
                {{ $task->title }}
            </div>
        @endforeach
    </div>

</div>
@endsection