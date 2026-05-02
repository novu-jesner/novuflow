<header class="sticky top-0 z-40 flex h-16 items-center gap-4 border-b bg-white px-6" x-data="{ mobileMenuOpen: false, notificationsOpen: false, userMenuOpen: false, notifications: [], unreadCount: 0 }">
    <!-- Mobile menu -->
    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 hover:bg-gray-100 rounded-md">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" x2="20" y1="12" y2="12"></line>
            <line x1="4" x2="20" y1="6" y2="6"></line>
            <line x1="4" x2="20" y1="18" y2="18"></line>
        </svg>
    </button>

    <!-- Mobile sidebar overlay -->
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" @click.away="mobileMenuOpen = false">
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r">
            <div class="flex h-16 shrink-0 items-center px-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect>
                            <rect width="3" height="3" x="7" y="7"></rect>
                            <rect width="3" height="3" x="14" y="7"></rect>
                            <rect width="3" height="3" x="7" y="14"></rect>
                            <rect width="3" height="3" x="14" y="14"></rect>
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-[#2a6a95]">NovuFlow</span>
                </div>
            </div>
            <div class="px-6 py-4">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                        <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                        <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                        <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                        <rect width="7" height="5" x="3" y="16" rx="1"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ url('/dashboard/projects') }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                        <path d="M3 3h18v18H3z"></path>
                        <path d="M9 3v18"></path>
                        <path d="M3 9h18"></path>
                    </svg>
                    Projects
                </a>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="flex-1 max-w-md">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <input type="search" placeholder="Search tasks, projects..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border-0 rounded-md focus:outline-none focus:ring-2 focus:ring-[#54acc8]">
        </div>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-4">
        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 hover:bg-gray-100 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                    <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                </svg>
                <span x-show="unreadCount > 0" class="absolute -right-1 -top-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center" x-text="unreadCount"></span>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50">
                <div class="p-3 border-b font-semibold">Notifications</div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="px-3 py-3 text-sm border-b last:border-0" :class="!notification.read ? 'bg-blue-50' : ''">
                            <div class="font-medium" x-text="notification.title"></div>
                            <div class="text-gray-600 text-xs mt-1" x-text="notification.message"></div>
                            <div class="text-gray-400 text-xs mt-1" x-text="new Date(notification.createdAt).toLocaleDateString()"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- User menu -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 p-1 hover:bg-gray-100 rounded-md">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center text-white font-semibold">
                    {{ auth()->user()->name ? auth()->user()->name[0] : 'U' }}
                </div>
                <div class="hidden md:block text-left">
                    <div class="text-sm">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->role ?? 'Employee' }}</div>
                </div>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50">
                <div class="p-3 border-b font-semibold">My Account</div>
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    My Account
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 text-left w-full">
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
