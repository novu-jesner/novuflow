@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-5xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="{
    activeTab: 'profile',
    isSubmitting: false,
    async updateProfile(e) {
        if (this.isSubmitting) return;
        this.isSubmitting = true;
        await submitForm(e.target, {
            resetForm: false,
            onSuccess: () => {
                this.isSubmitting = false;
                if (document.getElementById('current_password')) document.getElementById('current_password').value = '';
                if (document.getElementById('password')) document.getElementById('password').value = '';
                if (document.getElementById('password_confirmation')) document.getElementById('password_confirmation').value = '';
            },
            onError: () => {
                this.isSubmitting = false;
            }
        });
    }
}">
    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary to-secondary p-8 text-white shadow-lg mb-8">
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-2xl bg-accent/25 backdrop-blur-md border border-white/30 flex items-center justify-center text-4xl font-bold shadow-xl animate-pulse-slow">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold tracking-tight">{{ $user->name }}</h1>
                <p class="text-white/80 font-medium">{{ $user->email }}</p>
                <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                    <span class="px-3 py-1 rounded-full bg-accent/25 backdrop-blur-sm text-xs font-semibold uppercase tracking-wider border border-white/10">
                        {{ $user->role }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-accent/15 backdrop-blur-sm text-xs text-white/90">
                        Member since {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-64 h-64 bg-accent/15 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-48 h-48 bg-black/10 rounded-full blur-2xl"></div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex p-1 bg-muted/30 rounded-xl w-fit mb-8 shadow-inner border border-border">
        <button @click="activeTab = 'profile'" 
                :class="activeTab === 'profile' ? 'bg-card text-primary shadow-md' : 'text-muted-foreground hover:text-foreground'"
                class="px-6 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Personal Info
        </button>
        <button @click="activeTab = 'security'" 
                :class="activeTab === 'security' ? 'bg-card text-primary shadow-md' : 'text-muted-foreground hover:text-foreground'"
                class="px-6 py-2 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Security
        </button>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6 border-b border-border bg-muted/20">
                        <h3 class="text-lg font-bold text-foreground">Personal Information</h3>
                        <p class="text-sm text-muted-foreground">Keep your contact details up to date.</p>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST" @submit.prevent="updateProfile" class="p-8 space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="name" class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ $user->name }}" required class="w-full px-4 py-3 bg-surface border border-input rounded-xl focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-all text-foreground">
                            </div>
                            <div class="space-y-2">
                                <label for="email" class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ $user->email }}" required class="w-full px-4 py-3 bg-surface border border-input rounded-xl focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-all text-foreground">
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="px-8 py-3 bg-primary text-primary-foreground rounded-xl font-bold shadow-lg hover:opacity-95 transition-all flex items-center gap-2">
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <span x-text="isSubmitting ? 'Updating...' : 'Update Profile'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
                    <div class="p-6 border-b border-border bg-muted/20">
                        <h3 class="text-lg font-bold text-foreground">Password & Security</h3>
                        <p class="text-sm text-muted-foreground">Secure your account with a strong password.</p>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST" @submit.prevent="updateProfile" class="p-8 space-y-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label for="current_password" class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Current Password</label>
                                <input type="password" id="current_password" name="current_password" class="w-full px-4 py-3 bg-surface border border-input rounded-xl focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-all">
                            </div>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="password" class="text-xs font-bold text-muted-foreground uppercase tracking-wider">New Password</label>
                                    <input type="password" id="password" name="password" class="w-full px-4 py-3 bg-surface border border-input rounded-xl focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label for="password_confirmation" class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Confirm New Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 bg-surface border border-input rounded-xl focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-ring transition-all">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }" class="px-8 py-3 bg-primary text-primary-foreground rounded-xl font-bold shadow-lg hover:opacity-95 transition-all flex items-center gap-2">
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <span x-text="isSubmitting ? 'Updating...' : 'Update Password'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="space-y-6">
            <div class="bg-card rounded-2xl shadow-sm border border-border p-6">
                <h3 class="text-sm font-bold text-foreground mb-6 uppercase tracking-widest">Account Health</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-muted-foreground uppercase">Security</p>
                            <p class="text-sm font-semibold text-foreground">Protected</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-muted-foreground uppercase">Verified</p>
                            <p class="text-sm font-semibold text-foreground">Identity Confirmed</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-slate-800 dark:to-slate-950 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-sm font-bold mb-2 uppercase tracking-widest text-white/60">System Role</h3>
                    <p class="text-2xl font-bold mb-4">{{ $user->role }}</p>
                    <p class="text-xs text-white/50 leading-relaxed">
                        Your role determines your level of access and capabilities within the NovuFlow ecosystem.
                    </p>
                </div>
                <!-- Abstract background pattern -->
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-accent/10 rounded-full border border-white/10"></div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.9; transform: scale(0.98); }
    }
    .animate-pulse-slow {
        animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection

