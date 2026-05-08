<aside x-data="{
            storageKey: 'nv-hris-session-{{ auth()->id() }}',
            startedAt: null,
            timer: 0,
            intervalId: null,
            timedOut: false,
            modalOpen: false,
            isOffline: !navigator.onLine,
            init() {
                this.loadState();
                if (this.startedAt && !this.timedOut) {
                    this.setTimer();
                    this.ensureInterval();
                }
                window.addEventListener('online', () => this.isOffline = false);
                window.addEventListener('offline', () => this.isOffline = true);
            },
            today() {
                return new Date().toISOString().slice(0, 10);
            },
            loadState() {
                try {
                    const raw = localStorage.getItem(this.storageKey);
                    if (raw) {
                        const parsed = JSON.parse(raw);
                        if (parsed && parsed.date === this.today()) {
                            this.startedAt = parsed.startedAt || null;
                            this.timedOut = parsed.timedOut || false;
                        }
                    }
                } catch (_) {}
            },
            saveState() {
                try {
                    localStorage.setItem(this.storageKey, JSON.stringify({
                        date: this.today(),
                        startedAt: this.startedAt,
                        timedOut: this.timedOut,
                    }));
                } catch (_) {}
            },
            setTimer() {
                if (this.startedAt) {
                    this.timer = Date.now() - this.startedAt;
                } else {
                    this.timer = 0;
                }
            },
            ensureInterval() {
                if (!this.intervalId) {
                    this.intervalId = setInterval(() => this.setTimer(), 1000);
                }
            },
            clearTimer() {
                if (this.intervalId) {
                    window.clearInterval(this.intervalId);
                    this.intervalId = null;
                }
            },
            formatDuration(ms) {
                const totalSeconds = Math.floor(ms / 1000);
                const hours = Math.floor(totalSeconds / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                return `${hours}h ${minutes.toString().padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}s`;
            },
            get statusDotClasses() {
                if (this.isOffline) return 'bg-slate-400';
                if ($store.wellness.isOnLunch) return 'bg-orange-500';
                if (this.isActive) return 'bg-green-500 animate-pulse';
                return 'bg-red-500';
            },
            get statusTooltip() {
                if (this.isOffline) return 'Offline';
                if ($store.wellness.isOnLunch) return 'On Lunch';
                return this.isActive ? 'Active - Timed In' : 'Inactive - Timed Out';
            },
            get isActive() {
                return this.startedAt !== null && !this.timedOut;
            },
            get buttonText() {
                return this.isActive ? 'Clock-out' : 'Start Work';
            },
            get buttonClasses() {
                return this.isActive ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700';
            },
            handleSessionButton(event) {
                event.preventDefault();
                if (this.isActive) {
                    if (this.timer < 9 * 60 * 60 * 1000) {
                        this.modalOpen = true;
                        return;
                    }
                    this.finishSession();
                } else {
                    this.startSession();
                    window.open('{{ route('hris.time-in') }}', '_blank');
                }
            },
            startSession() {
                this.startedAt = Date.now();
                this.timedOut = false;
                this.saveState();
                this.setTimer();
                this.ensureInterval();
            },
            finishSession() {
                this.timedOut = true;
                this.startedAt = null;
                this.timer = 0;
                this.clearTimer();
                this.saveState();
                window.open('{{ route('hris.clock-out') }}', '_blank');
            },
            confirmClockOut(event) {
                event.preventDefault();
                this.modalOpen = false;
                this.finishSession();
            },
            cancelClockOut(event) {
                event.preventDefault();
                this.modalOpen = false;
            }
        }" x-init="init()" class="fixed inset-y-0 left-0 z-30 hidden w-64 bg-surface border-r border-border lg:block">
    <div class="flex h-16 shrink-0 items-center px-6">
        <div class="flex items-center gap-2">
            <img src="{{ asset('assets/novuflow_logo.png') }}" alt="NovuFlow Logo" class="h-8 w-auto">
            <span class="text-xl font-semibold text-primary">NovuFlow</span>
        </div>
    </div>
    <div class="px-6 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-foreground">{{ auth()->user()->name ?? 'User' }}</span>
                <div class="w-3 h-3 rounded-full ring-2 ring-white" :class="statusDotClasses" :title="statusTooltip"></div>
            </div>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs" :class="timedOut ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ($store.wellness.isOnLunch ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '{{ auth()->user()->last_hris_click_at?->isToday() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}')">
                <template x-if="$store.wellness.isOnLunch">
                    <span class="inline-flex items-center gap-1 transition-opacity duration-500" x-transition.opacity>🍱</span>
                </template>
                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path x-show="!$store.wellness.isOnLunch" d="M9 12l2 2 4-4"></path>
                </svg>
                <span x-text="timedOut ? 'Timed Out' : ($store.wellness.isOnLunch ? 'On Lunch' : '{{ auth()->user()->last_hris_click_at?->isToday() ? 'Timed In' : 'Not Timed In' }}')"></span>
            </span>
        </div>
    </div>

    <nav class="px-4 py-4 space-y-1">
        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="7" height="9" x="3" y="3" rx="1"></rect>
                <rect width="7" height="5" x="14" y="3" rx="1"></rect>
                <rect width="7" height="9" x="14" y="12" rx="1"></rect>
                <rect width="7" height="5" x="3" y="16" rx="1"></rect>
            </svg>
            Dashboard
        </a>
        <a href="{{ url('/dashboard/projects') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3h18v18H3z"></path>
                <path d="M9 3v18"></path>
                <path d="M3 9h18"></path>
            </svg>
            Projects
        </a>
        <a href="{{ url('/dashboard/my-tasks') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            My Tasks
        </a>
        <a href="{{ url('/dashboard/team') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            Team
        </a>
        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
            </svg>
            Notifications
        </a>

        <div class="pt-4 mt-4 border-t border-border">
            <div class="px-3 py-2 text-xs font-semibold text-muted-foreground uppercase">Quick Actions</div>
            <button type="button" @click="handleSessionButton($event)" title="Start work or clock out" :class="`${buttonClasses} transition-colors duration-300`" class="w-full flex items-center justify-center gap-3 px-3 py-2 rounded-md text-white animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12,6 12,12 16,14"></polyline>
                </svg>
                <span x-text="buttonText"></span>
            </button>
            <div class="mt-3 px-3 text-sm text-foreground/80">
                <div class="flex items-center justify-between">
                    <span class="font-medium">Work session</span>
                    <span class="text-xs text-muted-foreground" x-text="isActive ? 'Running' : (timedOut ? 'Timed Out' : 'Inactive')"></span>
                </div>
                <div class="mt-2 text-base font-semibold" x-text="isActive ? formatDuration(timer) : (timedOut ? 'Session ended' : 'Not started yet')"></div>
                <div x-show="isActive" class="mt-2 text-xs text-muted-foreground">
                    Started at <span x-text="new Date(startedAt).toLocaleTimeString([], { hour: '2-digit', minute:'2-digit' })"></span>
                </div>
            </div>
        </div>

        <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6" style="display:none;">
            <div @click.away="cancelClockOut($event)" class="w-full max-w-md rounded-xl bg-surface p-5 shadow-2xl border border-border">
                <h2 class="text-lg font-semibold text-foreground">Confirm early clock-out</h2>
                <p class="mt-3 text-sm text-muted-foreground">Are you sure you want to clock out? You have not yet completed your 9-hour shift.</p>
                <div class="mt-5 flex gap-3">
                    <button @click="confirmClockOut($event)" class="inline-flex flex-1 items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Yes, Clock Out</button>
                    <button @click="cancelClockOut($event)" class="inline-flex flex-1 items-center justify-center rounded-md bg-muted/20 px-4 py-2 text-sm font-semibold text-foreground hover:bg-muted/40 transition-colors">Cancel</button>
                </div>
            </div>
        </div>

        @if(auth()->check() && in_array(auth()->user()->role, ['SuperAdmin', 'Admin']))
        <div class="pt-4 mt-4 border-t border-border">
            <div class="px-3 py-2 text-xs font-semibold text-muted-foreground uppercase">Admin</div>
            <a href="{{ url('/dashboard/admin/users') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" x2="19" y1="8" y2="14"></line>
                    <line x1="22" x2="16" y1="11" y2="11"></line>
                </svg>
                Manage Users
            </a>
            <a href="{{ url('/dashboard/admin/teams') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Manage Teams
            </a>
            <a href="{{ url('/dashboard/admin/analytics') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-foreground/90 hover:bg-muted/50 hover:text-foreground transition-colors">
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
