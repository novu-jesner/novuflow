@extends('layouts.app')

@section('title', 'Notifications - NovuFlow')

@section('content')
<div class="flex h-screen bg-background text-foreground">
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        @include('partials.header')

        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-foreground">Notifications</h1>
                        <p class="text-muted-foreground text-sm mt-1">Manage your alerts and stay updated with your team's progress.</p>
                    </div>
                    
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-card border border-border rounded-lg text-sm font-medium text-foreground hover:bg-muted/20 transition-all shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
                    @forelse($notifications as $n)
                        @php
                            $data = $n->data;
                            $isRead = !is_null($n->read_at);
                            $type = $data['type'] ?? 'info';
                            
                            $iconColor = match($type) {
                                'task_commented' => 'bg-blue-500/15 text-blue-700 dark:text-blue-300',
                                'task_assigned' => 'bg-green-500/15 text-green-700 dark:text-green-300',
                                'project_invite' => 'bg-purple-500/15 text-purple-700 dark:text-purple-300',
                                default => 'bg-muted/40 text-muted-foreground',
                            };

                            $icon = match($type) {
                                'task_commented' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                                'task_assigned' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>',
                                'project_invite' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v7"/></svg>',
                                default => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>',
                            };

                            $url = match($type) {
                                'project_invite' => route('projects.invitation', $data['project_id']),
                                'task_assigned' => route('tasks.show', $data['task_id']),
                                'task_commented' => route('tasks.show', $data['task_id']) . '#comment-' . ($data['comment_id'] ?? ''),
                                default => '#',
                            };
                        @endphp

                        <div class="group relative flex gap-4 p-6 border-b border-border last:border-0 hover:bg-muted/20 transition-colors {{ !$isRead ? 'bg-primary/5' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $iconColor }}">
                                    {!! $icon !!}
                                </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-semibold text-foreground {{ !$isRead ? 'pr-6' : '' }}">
                                        {{ $data['title'] ?? 'Notification' }}
                                    </h3>
                                    <span class="text-xs text-muted-foreground whitespace-nowrap">{{ $n->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-muted-foreground text-sm mt-1 leading-relaxed">{{ $data['message'] ?? '' }}</p>
                                
                                @if(isset($data['comment_body']))
                                    <div class="mt-3 p-3 bg-muted/20 border border-border rounded-lg text-sm text-muted-foreground italic">
                                        "{{ Str::limit($data['comment_body'], 150) }}"
                                    </div>
                                @endif

                                <div class="mt-4 flex items-center gap-4">
                                    <a href="{{ route('notifications.show', $n->id) }}" onclick="event.stopPropagation();" class="text-sm font-semibold text-primary hover:opacity-90 flex items-center gap-1 transition-opacity">
                                        View Details
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                    </a>
                                    
                                    @if(!$isRead)
                                        <form action="{{ route('notifications.read', $n->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-muted-foreground hover:text-foreground transition-colors">
                                                Mark as read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            @if(!$isRead)
                                <div class="absolute right-6 top-6 w-2 h-2 rounded-full bg-primary"></div>
                            @endif
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-muted/30 border border-border rounded-full flex items-center justify-center mx-auto mb-4 text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                            </div>
                            <h3 class="text-lg font-medium text-foreground">No notifications yet</h3>
                            <p class="text-muted-foreground mt-1">When you get updates, they'll appear here.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
