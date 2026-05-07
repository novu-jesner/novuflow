@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data="{ activeTab: 'all' }">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-foreground">My Tasks</h1>
        <p class="text-muted-foreground mt-1">View and manage all your assigned tasks</p>
    </div>

    <!-- Tasks Tabs -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b border-border overflow-x-auto scrollbar-hide">
            <button @click="activeTab = 'all'" 
                class="px-4 py-3 text-sm font-medium border-b-2 transition-all whitespace-nowrap flex items-center gap-2" 
                :class="activeTab === 'all' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">
                All Tasks
                <span class="px-1.5 py-0.5 text-[10px] rounded-full" :class="activeTab === 'all' ? 'bg-primary text-white' : 'bg-muted/40 text-muted-foreground border border-border'">{{ $tasks->count() }}</span>
            </button>
            @foreach($statuses as $status)
            @php $slug = Str::slug($status); @endphp
            <button @click="activeTab = '{{ $slug }}'" 
                class="px-4 py-3 text-sm font-medium border-b-2 transition-all whitespace-nowrap flex items-center gap-2" 
                :class="activeTab === '{{ $slug }}' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">
                {{ $status }}
                <span class="px-1.5 py-0.5 text-[10px] rounded-full" :class="activeTab === '{{ $slug }}' ? 'bg-primary text-white' : 'bg-muted/40 text-muted-foreground border border-border'">{{ $groupedTasks->get($status)->count() }}</span>
            </button>
            @endforeach
        </div>

        <!-- All Tasks Tab -->
        <div x-show="activeTab === 'all'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse($tasks as $task)
                @include('employee.partials.task-card', ['task' => $task])
            @empty
                <div class="col-span-full py-12 text-center bg-card rounded-xl border border-dashed border-border">
                    <p class="text-muted-foreground">No tasks assigned to you yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Dynamic Status Tabs -->
        @foreach($statuses as $status)
        @php $slug = Str::slug($status); @endphp
        <div x-show="activeTab === '{{ $slug }}'" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3" style="display: none;">
            @forelse($groupedTasks->get($status) as $task)
                @include('employee.partials.task-card', ['task' => $task])
            @empty
                <div class="col-span-full py-12 text-center bg-card rounded-xl border border-dashed border-border">
                    <p class="text-muted-foreground">No tasks in {{ $status }}.</p>
                </div>
            @endforelse
        </div>
        @endforeach
    </div>
</div>
@endsection
