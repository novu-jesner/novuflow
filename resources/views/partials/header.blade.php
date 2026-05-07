<header class="sticky top-0 z-40 flex h-16 items-center gap-4 border-b border-border bg-surface/90 px-6 backdrop-blur supports-[backdrop-filter]:bg-surface/75 transition-colors" 
    x-data="{ 
        mobileMenuOpen: false, 
        notificationsOpen: false, 
        userMenuOpen: false, 
        notifications: {{ auth()->user()->notifications()->latest()->take(10)->get()->map(function($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'comment_body' => $n->data['comment_body'] ?? null,
                'type' => $n->data['type'] ?? 'info',
                'project_id' => $n->data['project_id'] ?? null,
                'task_id' => $n->data['task_id'] ?? null,
                'comment_id' => $n->data['comment_id'] ?? null,
                'read' => !is_null($n->read_at),
                'createdAt' => $n->created_at->toISOString()
            ];
        })->toJson() }}, 
        unreadCount: {{ auth()->user()->unreadNotifications()->count() }},
        init() {
            const setupEcho = () => {
                if (window.Echo) {
                    window.Echo.private('App.Models.User.{{ auth()->id() }}')
                        .notification((notification) => {
                            // Prepend the new notification to the list
                            this.notifications.unshift({
                                id: notification.id,
                                title: notification.title,
                                message: notification.message,
                                comment_body: notification.comment_body,
                                type: notification.type,
                                project_id: notification.project_id,
                                task_id: notification.task_id,
                                comment_id: notification.comment_id,
                                read: false,
                                createdAt: new Date().toISOString()
                            });
                            
                            // Keep only the last 10 for the dropdown
                            if (this.notifications.length > 10) {
                                this.notifications.pop();
                            }
                            
                            this.unreadCount++;
                            
                            // Show a toast
                            if (window.Alpine && Alpine.store('toast')) {
                                Alpine.store('toast').show(notification.message, 'info');
                            }
                        });
                } else {
                    // If Echo isn't ready yet, try again in 500ms
                    setTimeout(setupEcho, 500);
                }
            };
            setupEcho();
        },
        async markAsRead(id) {
            window.location.href = `/notifications/${id}`;
        },
        async markAllRead() {
            try {
                await fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                this.notifications.forEach(n => n.read = true);
                this.unreadCount = 0;
            } catch (e) { console.error(e); }
        }
    }">
    <!-- Mobile menu -->
    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 hover:bg-muted/60 rounded-md transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" x2="20" y1="12" y2="12"></line>
            <line x1="4" x2="20" y1="6" y2="6"></line>
            <line x1="4" x2="20" y1="18" y2="18"></line>
        </svg>
    </button>

    <!-- Mobile sidebar overlay -->
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" @click.away="mobileMenuOpen = false">
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-surface border-r border-border">
            <div class="flex h-16 shrink-0 items-center px-6">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-8 w-auto">
                    <span class="text-xl font-semibold text-primary">NovuFlow</span>
                </div>
            </div>
            <div class="px-6 py-4">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 p-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ url('/dashboard/projects') }}" class="flex items-center gap-3 p-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                        <path d="M3 3h18v18H3z"></path>
                        <path d="M9 3v18"></path>
                        <path d="M3 9h18"></path>
                    </svg>
                    Projects
                </a>
            </div>
        </div>
    </div>

    <!-- Spacer to push items to the right -->
    <div class="flex-1"></div>

    <!-- Right Side Controls -->
    <div class="flex items-center gap-4 sm:gap-6">
        <!-- Theme toggle -->
        <button
            type="button"
            class="inline-flex items-center justify-center rounded-md p-2 hover:bg-muted/60 focus:outline-none focus:ring-2 focus:ring-ring/50 transition-colors"
            x-cloak
            :aria-pressed="$store.theme.isDark"
            :aria-label="$store.theme.isDark ? 'Switch to light mode' : 'Switch to dark mode'"
            @click="$store.theme.toggle()"
        >
            <svg x-show="!$store.theme.isDark" x-transition.opacity class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2"></path><path d="M12 20v2"></path>
                <path d="M4.93 4.93l1.41 1.41"></path><path d="M17.66 17.66l1.41 1.41"></path>
                <path d="M2 12h2"></path><path d="M20 12h2"></path>
                <path d="M6.34 17.66l-1.41 1.41"></path><path d="M19.07 4.93l-1.41 1.41"></path>
            </svg>
            <svg x-show="$store.theme.isDark" x-transition.opacity class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>

        <!-- Search -->
        <div class="relative w-48 lg:w-64">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input type="search" placeholder="Search..." class="w-full pl-10 pr-4 py-2 bg-muted/40 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-ring/50 text-sm transition-colors">
        </div>
        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 hover:bg-muted/60 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
                <span x-show="unreadCount > 0" class="absolute -right-1 -top-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center" x-text="unreadCount"></span>
            </button>
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 @click.away="open = false" 
                 class="absolute right-0 mt-2 w-80 bg-popover rounded-lg shadow-xl border border-border z-50 overflow-hidden" style="display: none;">
                <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
                    <span class="font-semibold text-sm text-foreground">Notifications</span>
                    <button x-show="unreadCount > 0" @click="markAllRead()" class="text-[11px] font-medium text-primary hover:opacity-90 transition-opacity">
                        Mark all as read
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="px-4 py-3 text-sm border-b border-border last:border-0 hover:bg-muted/30 cursor-pointer flex gap-3 transition-colors" 
                             :class="!notification.read ? 'bg-primary/5' : ''"
                             @click="markAsRead(notification.id)">
                            
                            <!-- Icon based on type -->
                            <div class="shrink-0 mt-0.5">
                                <template x-if="notification.type === 'task_commented'">
                                    <div class="w-8 h-8 rounded-full bg-blue-500/15 text-blue-600 dark:text-blue-300 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    </div>
                                </template>
                                <template x-if="notification.type === 'task_assigned'">
                                    <div class="w-8 h-8 rounded-full bg-green-500/15 text-green-600 dark:text-green-300 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                    </div>
                                </template>
                                <template x-if="notification.type === 'project_invite'">
                                    <div class="w-8 h-8 rounded-full bg-purple-500/15 text-purple-600 dark:text-purple-300 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v7"/></svg>
                                    </div>
                                </template>
                                <template x-if="!['task_commented', 'task_assigned', 'project_invite'].includes(notification.type)">
                                    <div class="w-8 h-8 rounded-full bg-muted/60 text-muted-foreground flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>
                                    </div>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-foreground truncate" x-text="notification.title"></div>
                                <div class="text-muted-foreground text-xs mt-0.5 line-clamp-2 leading-relaxed" x-text="notification.message"></div>
                                <div x-show="notification.comment_body" class="mt-1 px-2 py-1 bg-muted/30 rounded text-[10px] text-muted-foreground italic truncate border border-border" x-text="'&quot;' + notification.comment_body + '&quot;'"></div>
                                <div class="text-muted-foreground/80 text-[10px] mt-1" x-text="new Date(notification.createdAt).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></div>
                            </div>

                            <div x-show="!notification.read" class="shrink-0 mt-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-primary"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-muted-foreground text-sm">
                        No new notifications
                    </div>
                </div>
                <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-center text-xs font-semibold text-primary bg-muted/20 hover:bg-muted/40 transition-colors border-t border-border">
                    View All Notifications
                </a>
            </div>
        </div>

        <!-- User menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 p-1 hover:bg-muted/60 rounded-md transition-colors">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-semibold">
                    {{ auth()->user()->name ? auth()->user()->name[0] : 'U' }}
                </div>
                <div class="hidden md:block text-left">
                    <div class="text-sm">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-xs text-muted-foreground">{{ auth()->user()->role ?? 'Employee' }}</div>
                </div>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-52 bg-popover rounded-lg shadow-lg border border-border z-50 overflow-hidden" style="display:none;">
                <div class="p-3 border-b border-border font-semibold text-foreground bg-muted/10">My Account</div>
                <a href="{{ route('profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-muted/30 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    My Account
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-muted/30 text-left w-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" x2="9" y1="12" y2="12"></line>
                        </svg>
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
