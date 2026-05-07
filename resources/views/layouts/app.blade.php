<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovuFlow')</title>
    <script>
        // Prevent flash of incorrect theme (runs before CSS/DOM paint).
        (function () {
            try {
                const storageKey = 'nv-theme'; // 'light' | 'dark' | 'system'
                const saved = localStorage.getItem(storageKey);
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const mode = saved || 'system';
                const isDark = mode === 'dark' || (mode === 'system' && prefersDark);
                const root = document.documentElement;
                if (isDark) root.classList.add('dark');
                else root.classList.remove('dark');
                root.style.colorScheme = isDark ? 'dark' : 'light';
            } catch (_) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                storageKey: 'nv-theme', // 'light' | 'dark' | 'system'
                mode: 'system',
                systemPrefersDark: false,
                media: null,

                init() {
                    this.mode = this.getSavedMode();
                    this.media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
                    this.systemPrefersDark = this.media ? this.media.matches : false;

                    // Apply immediately.
                    this.apply();

                    // Live system preference updates.
                    if (this.media) {
                        const handler = (e) => {
                            this.systemPrefersDark = !!e.matches;
                            if (this.mode === 'system') this.apply();
                        };
                        try {
                            this.media.addEventListener('change', handler);
                        } catch (_) {
                            // Safari <14
                            this.media.addListener(handler);
                        }
                    }
                },

                getSavedMode() {
                    try {
                        const v = localStorage.getItem(this.storageKey);
                        return (v === 'light' || v === 'dark' || v === 'system') ? v : 'system';
                    } catch (_) {
                        return 'system';
                    }
                },

                get isDark() {
                    return this.mode === 'dark' || (this.mode === 'system' && this.systemPrefersDark);
                },

                apply(withTransition = false) {
                    const root = document.documentElement;
                    if (withTransition) {
                        root.classList.add('theme-transition');
                        window.setTimeout(() => root.classList.remove('theme-transition'), 220);
                    }

                    root.classList.toggle('dark', this.isDark);
                    root.style.colorScheme = this.isDark ? 'dark' : 'light';
                },

                setMode(mode) {
                    this.mode = mode;
                    try { localStorage.setItem(this.storageKey, mode); } catch (_) {}
                    this.apply(true);
                },

                toggle() {
                    this.setMode(this.isDark ? 'light' : 'dark');
                },
            });

            Alpine.store('toast', {
                items: [],
                counter: 0,
                show(message, type = 'success', duration = 3500) {
                    const id = ++this.counter;
                    this.items.push({ id, message, type });
                    setTimeout(() => this.remove(id), duration);
                },
                remove(id) {
                    this.items = this.items.filter(n => n.id !== id);
                }
            });

            Alpine.store('wellness', {
                storageKey: 'nv-wellness-{{ auth()->id() }}',
                isOnLunch: false,
                hasNotifiedLunch: false,
                hasNotifiedReturn: false,
                healthTips: ['Hydrate 💧', 'Stretch your back 🧘', 'Rest your eyes 👁️'],
                currentDate: null,

                init() {
                    this.loadState();
                    this.checkTime();
                    setInterval(() => this.checkTime(), 60000);
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
                                this.hasNotifiedLunch = parsed.hasNotifiedLunch || false;
                                this.hasNotifiedReturn = parsed.hasNotifiedReturn || false;
                            }
                        }
                    } catch (_) {
                        // ignore malformed data
                    }
                    this.currentDate = this.today();
                },

                saveState() {
                    try {
                        localStorage.setItem(this.storageKey, JSON.stringify({
                            date: this.today(),
                            hasNotifiedLunch: this.hasNotifiedLunch,
                            hasNotifiedReturn: this.hasNotifiedReturn,
                        }));
                    } catch (_) {}
                },

                checkTime() {
                    const now = new Date();
                    const today = this.today();
                    const day = now.getDay();
                    const minutes = now.getHours() * 60 + now.getMinutes();
                    const isWeekday = day >= 1 && day <= 5;

                    if (this.currentDate !== today) {
                        this.currentDate = today;
                        this.hasNotifiedLunch = false;
                        this.hasNotifiedReturn = false;
                        this.saveState();
                    }

                    this.isOnLunch = isWeekday && minutes >= 690 && minutes < 750;

                    if (!isWeekday) {
                        return;
                    }

                    if (minutes >= 690 && minutes < 750 && !this.hasNotifiedLunch) {
                        this.notifyLunch();
                    }

                    if (minutes >= 750 && !this.hasNotifiedReturn) {
                        this.notifyReturn();
                    }
                },

                notifyLunch() {
                    const tip = this.healthTips[Math.floor(Math.random() * this.healthTips.length)];
                    const message = `It’s 11:30 AM! Time for your scheduled lunch break. 🍱 Health is wealth—recharge and enjoy! ${tip}`;
                    Alpine.store('toast').show(message, 'info', 20000);
                    this.showBrowserNotification('Lunch Break', message);
                    this.hasNotifiedLunch = true;
                    this.saveState();
                },

                notifyReturn() {
                    const message = 'Welcome back! Hope you had a refreshing break.';
                    Alpine.store('toast').show(message, 'success', 20000);
                    this.showBrowserNotification('Welcome Back', message);
                    this.hasNotifiedReturn = true;
                    this.saveState();
                },

                showBrowserNotification(title, body) {
                    if (!('Notification' in window)) {
                        return;
                    }
                    if (Notification.permission === 'granted') {
                        new Notification(title, { body });
                    } else if (Notification.permission !== 'denied') {
                        Notification.requestPermission().then((permission) => {
                            if (permission === 'granted') {
                                new Notification(title, { body });
                            }
                        });
                    }
                },
            });
        });

        async function submitForm(form, opts = {}) {
            const btn = form.querySelector('[type="submit"]');
            const origText = btn ? btn.innerHTML : '';
            if (btn) { btn.disabled = true; btn.innerHTML = 'Processing…'; }

            form.querySelectorAll('.field-error').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: new FormData(form),
                });
                const data = await res.json();

                if (res.ok) {
                    Alpine.store('toast').show(data.message || 'Success!', 'success');
                    if (opts.resetForm !== false) form.reset();
                    if (opts.onSuccess) opts.onSuccess(data);
                } else if (res.status === 422) {
                    Object.entries(data.errors || {}).forEach(([field, msgs]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('border-red-500');
                            const p = document.createElement('p');
                            p.className = 'field-error text-sm text-red-600 mt-1';
                            p.textContent = msgs[0];
                            input.parentNode.appendChild(p);
                        }
                    });
                    Alpine.store('toast').show('Please fix the errors below.', 'error');
                } else {
                    Alpine.store('toast').show(data.message || 'Something went wrong.', 'error');
                }
                return data;
            } catch (e) {
                Alpine.store('toast').show('Network error. Please try again.', 'error');
            } finally {
                if (btn) { btn.disabled = false; btn.innerHTML = origText; }
            }
        }

        async function ajaxDelete(url, opts = {}) {
            if (!confirm(opts.confirm || 'Are you sure?')) return;
            try {
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    Alpine.store('toast').show(data.message || 'Deleted!', 'success');
                    if (opts.onSuccess) opts.onSuccess(data);
                } else {
                    Alpine.store('toast').show(data.message || 'Delete failed.', 'error');
                }
                return data;
            } catch (e) {
                Alpine.store('toast').show('Network error. Please try again.', 'error');
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Alpine.store('toast').show("{{ session('success') }}", 'success');
            @endif
            @if(session('error'))
                Alpine.store('toast').show("{{ session('error') }}", 'error');
            @endif

            if (window.Alpine && Alpine.store('wellness')) {
                Alpine.store('wellness').init();
            }
        });
    </script>
</head>
<body class="min-h-screen bg-background text-foreground">
    <!-- Toast Notifications -->
    <div x-data class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none" x-show="$store.toast.items.length > 0" style="display:none;">
        <template x-for="t in $store.toast.items" :key="t.id">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white text-sm w-72 max-w-md"
                 :class="{ 'bg-green-600/90': t.type==='success', 'bg-red-600/90': t.type==='error', 'bg-blue-600/90': t.type==='info' }">
                <template x-if="t.type==='success'">
                    <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </template>
                <template x-if="t.type==='error'">
                    <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                </template>
                <template x-if="t.type==='info'">
                    <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>
                </template>
                <span x-text="t.message" class="flex-1"></span>
                <button @click="$store.toast.remove(t.id)" class="ml-1 hover:opacity-75 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                </button>
            </div>
        </template>
    </div>

    @yield('content')
</body>
</html>
