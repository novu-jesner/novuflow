<aside class="fixed inset-y-0 left-0 z-30 hidden w-64 bg-white border-r lg:block">
    <div class="flex h-16 shrink-0 items-center px-6">
        <div class="flex items-center gap-2">
            <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-8 w-auto">
            <span class="text-xl font-semibold text-[#2a6a95]">NovuFlow</span>
        </div>
    </div>

    <nav class="px-4 py-4 space-y-1">
        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                <rect width="7" height="5" x="3" y="16" rx="1"></rect>
            </svg>
            Dashboard
        </a>
        <a href="{{ url('/dashboard/projects') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3h18v18H3z"></path>
                <path d="M9 3v18"></path>
                <path d="M3 9h18"></path>
            </svg>
            Projects
        </a>
        <a href="{{ url('/dashboard/my-tasks') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            My Tasks
        </a>
        <a href="{{ url('/dashboard/team') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            Team
        </a>

        @if(auth()->check() && in_array(auth()->user()->role, ['SuperAdmin', 'Admin']))
        <div class="pt-4 mt-4 border-t">
            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Admin</div>
            <a href="{{ url('/dashboard/admin/users') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" x2="19" y1="8" y2="14"></line>
                    <line x1="22" x2="16" y1="11" y2="11"></line>
                </svg>
                Manage Users
            </a>
            <a href="{{ url('/dashboard/admin/teams') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Manage Teams
            </a>
            <a href="{{ url('/dashboard/admin/analytics') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" x2="18" y1="20" y2="10"></line>
                    <line x1="12" x2="12" y1="20" y2="4"></line>
                    <line x1="6" x2="6" y1="20" y2="14"></line>
                </svg>
                Analytics
            </a>
        </div>
        @endif
    </nav>
</aside>
