@extends('layouts.dashboard')

@section('dashboard-content')
<div class="space-y-6" x-data='{
        activeTab: "{{ $activeTab }}",
        selectedProject: "{{ $selectedProjectId }}",
        heatmap: @json($heatmap),
        timeline: @json($timeline),
        playbackTasks: @json($playbackTasks),
        columns: @json($selectedProjectColumns ? $selectedProjectColumns->pluck("name")->toArray() : []),
        playbackIndex: 0,
        playing: false,
        interval: null,
        speed: 1000,
        loading: false,
        init() {
            this.$watch("activeTab", (val) => {
                const url = new URL(window.location.href);
                url.searchParams.set("tab", val);
                window.history.replaceState(null, "", url.toString());
            });
        },
        play() {
            if (!this.timeline.length) return;
            if (this.playing) return;
            this.playing = true;
            if (this.playbackIndex >= this.timeline.length - 1) {
                this.playbackIndex = 0;
            }
            this.interval = setInterval(() => {
                if (this.playbackIndex < this.timeline.length - 1) {
                    this.playbackIndex++;
                } else {
                    this.pause();
                }
            }, this.speed);
        },
        pause() {
            this.playing = false;
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },
        updateSpeed(newSpeed) {
            this.speed = newSpeed;
            if (this.playing) {
                this.pause();
                this.play();
            }
        },
        next() {
            this.pause();
            if (this.playbackIndex < this.timeline.length - 1) {
                this.playbackIndex++;
            }
        },
        prev() {
            this.pause();
            if (this.playbackIndex > 0) {
                this.playbackIndex--;
            }
        },
        restart() {
            this.pause();
            this.playbackIndex = 0;
        },
        get currentEvent() {
            return this.timeline[this.playbackIndex] || null;
        },
        get board() {
            if (!this.timeline.length) {
                const emptyBoard = {};
                this.columns.forEach(col => { emptyBoard[col] = []; });
                return emptyBoard;
            }
            const targetTime = new Date(this.timeline[this.playbackIndex].timestamp);
            const board = {};
            this.columns.forEach(col => { board[col] = []; });
            
            this.playbackTasks.forEach(task => {
                const taskCreatedAt = new Date(task.created_at);
                if (taskCreatedAt > targetTime) return;
                
                let currentStatus = task.initial_status;
                for (let i = 0; i < task.history.length; i++) {
                    const transitionTime = new Date(task.history[i].changed_at);
                    if (transitionTime <= targetTime) {
                        currentStatus = task.history[i].new_status;
                      } else {
                          break;
                      }
                }
                
                if (board[currentStatus]) {
                    board[currentStatus].push(task);
                }
            });
            return board;
        },
        inspectingTask: null,
        get lastMovedTaskId() {
            return this.currentEvent ? this.currentEvent.task_id : null;
        },
        badgeClass(intensity) {
            return intensity === "critical"
                ? "bg-rose-50 border border-rose-200 text-rose-700 dark:bg-rose-950/20 dark:border-rose-900/30 dark:text-rose-300"
                : intensity === "warning"
                    ? "bg-amber-50 border border-amber-200 text-amber-700 dark:bg-amber-950/20 dark:border-amber-900/30 dark:text-amber-300"
                    : "bg-emerald-50 border border-emerald-200 text-emerald-700 dark:bg-emerald-950/20 dark:border-emerald-900/30 dark:text-emerald-300";
        },
        isStalledAtTime(task, columnName) {
            if (columnName === "Completed") return false;
            if (!this.timeline.length) return false;
            
            const targetTime = new Date(this.timeline[this.playbackIndex].timestamp);
            
            // Find the time task entered this column
            let enteredTime = null;
            if (task.initial_status === columnName) {
                enteredTime = new Date(task.created_at);
            }
            
            for (let i = 0; i < task.history.length; i++) {
                if (task.history[i].new_status === columnName) {
                    enteredTime = new Date(task.history[i].changed_at);
                }
            }
            
            if (!enteredTime) return false;
            
            // Find when it left this column
            let leftTime = null;
            for (let i = 0; i < task.history.length; i++) {
                const transitionTime = new Date(task.history[i].changed_at);
                if (transitionTime > enteredTime && transitionTime <= targetTime) {
                    // It left this column before the target playback moment
                    leftTime = transitionTime;
                }
            }
            
            const endTime = leftTime || targetTime;
            const durationMs = endTime - enteredTime;
            return durationMs > 172800 * 1000; // > 48 hours in ms
        }
    }'>
    <!-- Loading overlay -->
    <div x-show="loading" class="fixed inset-0 z-[60] flex items-center justify-center bg-background/60 backdrop-blur-xs transition-opacity duration-300" style="display: none;">
        <div class="flex flex-col items-center gap-3 bg-card border border-border p-6 rounded-2xl shadow-2xl">
            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-semibold text-foreground">Loading Analytics...</span>
        </div>
    </div>

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-semibold text-foreground">Analytics</h1>
        <p class="text-muted-foreground mt-1">System-wide analytics and insights</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="bg-card border border-border rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Total Projects</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                    <path d="M3 3h18v18H3z"></path>
                    <path d="M9 3v18"></path>
                    <path d="M3 9h18"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $totalProjects }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">Active projects</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Tasks Completed</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $completedTasks }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">Tasks done</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Active Tasks</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $activeTasks }}</div>
                <p class="text-xs text-blue-600 flex items-center gap-1 mt-1">In progress</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow p-6">
            <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Team Members</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $teamMembers }}</div>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-1">Team members</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="space-y-4">
        <div class="flex gap-2 border-b border-border">
            <button @click="activeTab = 'overview'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'overview' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">Overview</button>
            <button @click="activeTab = 'tasks'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'tasks' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">Tasks</button>
            <button @click="activeTab = 'teams'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'teams' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">Teams</button>
            <button @click="activeTab = 'projects'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'projects' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">Projects</button>
            <button @click="activeTab = 'kanban'" class="px-4 py-2 text-sm font-medium border-b-2 transition-colors" :class="activeTab === 'kanban' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground'">Kanban Playback</button>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="grid gap-4 lg:grid-cols-2">
            @php
                $totalTaskCount = $completedTasks + $activeTasks;
                $completedPercent = $totalTaskCount > 0 ? round(($completedTasks / $totalTaskCount) * 100) : 0;
                $activePercent = $totalTaskCount > 0 ? round(($activeTasks / $totalTaskCount) * 100) : 0;
            @endphp
            <div class="bg-card border border-border rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-border">
                    <h2 class="font-semibold">Task Overview</h2>
                    <p class="text-sm text-muted-foreground">Task completion statistics</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-muted-foreground">Total Tasks</span>
                            <span class="font-semibold">{{ $totalTaskCount }}</span>
                        </div>
                        <div class="w-full bg-muted/50 rounded-full h-4 overflow-hidden border border-border">
                            <div class="bg-green-500 h-4 rounded-full" style="width: {{ $completedPercent }}%"></div>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-green-600">{{ $completedTasks }} Completed ({{ $completedPercent }}%)</span>
                            <span class="text-blue-600">{{ $activeTasks }} Active ({{ $activePercent }}%)</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-border">
                        <h4 class="font-medium mb-3">Quick Stats</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-muted/30 p-3 rounded border border-border">
                                <div class="text-xs text-muted-foreground">Projects</div>
                                <div class="text-lg font-semibold">{{ $totalProjects }}</div>
                            </div>
                            <div class="bg-muted/30 p-3 rounded border border-border">
                                <div class="text-xs text-muted-foreground">Team Members</div>
                                <div class="text-lg font-semibold">{{ $teamMembers }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-card border border-border rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-border">
                    <h2 class="font-semibold">System Status</h2>
                    <p class="text-sm text-muted-foreground">Current system overview</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">All Systems Operational</div>
                                <div class="text-sm text-muted-foreground">Last checked: Just now</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">{{ $activeTasks }} Active Tasks</div>
                                <div class="text-sm text-muted-foreground">Requiring attention</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">{{ $teamMembers }} Team Members</div>
                                <div class="text-sm text-muted-foreground">Across all teams</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" class="bg-card border border-border rounded-lg shadow p-6" style="display: none;">
            <h3 class="font-semibold mb-4">Task Statistics</h3>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-muted/30 border border-border p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $completedTasks }}</div>
                    <div class="text-sm text-muted-foreground">Completed</div>
                </div>
                <div class="bg-muted/30 border border-border p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $activeTasks }}</div>
                    <div class="text-sm text-muted-foreground">Active</div>
                </div>
                <div class="bg-muted/30 border border-border p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-muted-foreground">{{ $totalTaskCount }}</div>
                    <div class="text-sm text-muted-foreground">Total</div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'teams'" class="bg-card border border-border rounded-lg shadow overflow-hidden" style="display: none;">
            <div class="p-6 border-b border-border">
                <h3 class="font-semibold">Team Overview</h3>
                <p class="text-sm text-muted-foreground">Complete list of all teams in the system</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-muted/20 text-muted-foreground text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 font-medium">Team & Leader</th>
                            <th class="px-6 py-3 font-medium">Projects</th>
                            <th class="px-6 py-3 font-medium">Task Completion</th>
                            <th class="px-6 py-3 font-medium text-center">Overdue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($teams as $team)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-foreground">{{ $team->name }}</span>
                                    <span class="text-xs text-muted-foreground">Lead: {{ $team->leader->name ?? 'None' }} • {{ $team->members_count }} members</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-border">
                                    {{ $team->projects_count }} Projects
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full max-w-[100px]">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-semibold text-primary">{{ $team->completion_rate }}%</span>
                                    </div>
                                    <div class="w-full bg-muted/40 border border-border rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ $team->completion_rate }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($team->overdue_tasks > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-destructive/15 text-destructive border border-border">
                                        {{ $team->overdue_tasks }} Overdue
                                    </span>
                                @else
                                    <span class="text-xs text-muted-foreground">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-muted-foreground italic">No teams found in the system.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Projects Tab -->
        <div x-show="activeTab === 'projects'" class="bg-card border border-border rounded-lg shadow p-6" style="display: none;">
            <h3 class="font-semibold mb-4">Project Statistics</h3>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-muted/30 border border-border p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $totalProjects }}</div>
                    <div class="text-sm text-muted-foreground">Total Projects</div>
                </div>
                <div class="bg-muted/30 border border-border p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $completedTasks }}</div>
                    <div class="text-sm text-muted-foreground">Tasks Completed</div>
                </div>
                <div class="bg-muted/30 border border-border p-4 rounded-lg text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $activeTasks }}</div>
                    <div class="text-sm text-muted-foreground">Tasks In Progress</div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'kanban'" class="bg-card border border-border rounded-xl shadow-lg p-6 space-y-6" style="display: none;">
            <!-- Header section -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between border-b border-border pb-6">
                <div>
                    <h3 class="text-xl font-bold text-foreground">
                        Kanban Flow & Blocker Analytics
                    </h3>
                    <p class="text-xs text-muted-foreground mt-1">Identify board bottlenecks, inspect detailed task histories, and replay task migration step-by-step.</p>
                </div>
                <form method="GET" action="{{ route('admin.analytics') }}" class="flex items-center gap-3" @submit="loading = true">
                    <input type="hidden" name="tab" :value="activeTab">
                    <select name="project_id" class="rounded-lg border border-border bg-background px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" @change="$el.form.requestSubmit()">
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected($project->id == $selectedProjectId)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground transition hover:bg-primary/95 shadow-md flex items-center gap-2">
                        <svg x-show="loading" class="animate-spin h-4 w-4 text-primary-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Refreshing...' : 'Refresh'"></span>
                    </button>
                </form>
            </div>

            <!-- Heatmap section -->
            <div class="space-y-3">
                <h4 class="text-xs font-extrabold text-muted-foreground uppercase tracking-wider">Blocker Heatmap (Cumulative Column Idle Durations)</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <template x-for="bucket in heatmap" :key="bucket.column">
                        <div :class="{
                            'bg-gradient-to-br from-rose-500/10 to-rose-500/5 border-rose-200/50 dark:border-rose-900/30 text-rose-900 dark:text-rose-200 shadow-sm shadow-rose-500/5': bucket.intensity === 'critical',
                            'bg-gradient-to-br from-amber-500/10 to-amber-500/5 border-amber-200/50 dark:border-amber-900/30 text-amber-900 dark:text-amber-200 shadow-sm shadow-amber-500/5': bucket.intensity === 'warning',
                            'bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 border-emerald-200/50 dark:border-emerald-900/30 text-emerald-900 dark:text-emerald-200 shadow-sm shadow-emerald-500/5': bucket.intensity === 'safe'
                        }" class="rounded-2xl border p-5 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                            <div>
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <h5 class="font-bold text-sm truncate" x-text="bucket.column"></h5>
                                    <span :class="badgeClass(bucket.intensity)" class="rounded-full px-2.5 py-0.5 text-[9px] font-extrabold uppercase tracking-wider" x-text="bucket.intensity"></span>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-2xl font-black tracking-tight" x-text="bucket.duration_label"></div>
                                    <div class="text-[10px] opacity-75 uppercase font-bold tracking-wide">Total Cumulative Time</div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-t border-current/10 space-y-1 text-xs opacity-90">
                                <div class="flex justify-between">
                                    <span>Average stay time:</span>
                                    <span class="font-bold" x-text="bucket.avg_duration_label"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Active tasks:</span>
                                    <span class="font-bold" x-text="bucket.active_count"></span>
                                </div>
                            </div>

                            <!-- Stalled Tasks list inside card -->
                            <div class="mt-4 border-t border-current/10 pt-3" x-show="bucket.stalled_tasks && bucket.stalled_tasks.length > 0">
                                <p class="text-xs font-bold text-rose-800 dark:text-rose-300 mb-2 flex items-center gap-1.5">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-500 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                                    </span>
                                    Stalled Tasks (<span x-text="bucket.stalled_tasks.length"></span>)
                                </p>
                                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1 scrollbar-thin">
                                    <template x-for="stalled in bucket.stalled_tasks" :key="stalled.id">
                                        <div class="text-[11px] bg-rose-500/5 hover:bg-rose-500/10 border border-rose-200/40 dark:border-rose-900/30 rounded-lg p-2 transition-colors">
                                            <div class="flex justify-between font-bold text-foreground">
                                                <span class="truncate pr-1" x-text="stalled.title"></span>
                                                <span class="shrink-0 text-[8px] font-extrabold uppercase px-1 rounded bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-300" x-text="stalled.priority"></span>
                                            </div>
                                            <div class="flex justify-between text-[10px] text-muted-foreground mt-1">
                                                <span>Stuck <span x-text="stalled.duration_label"></span></span>
                                                <span x-text="stalled.since"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Visual Playback Section -->
            <div class="border-t border-border pt-6 space-y-4">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h4 class="text-sm font-bold text-muted-foreground uppercase tracking-wider">Visual Board Time-lapse</h4>
                        <p class="text-xs text-muted-foreground mt-0.5" x-show="timeline.length > 0">Watch how tasks migrated through the board step-by-step. Click on any task card to inspect its lifecycle history.</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs" x-show="timeline.length > 0">
                        <span class="font-semibold text-muted-foreground">Playback Speed:</span>
                        <div class="inline-flex rounded-lg border border-border bg-muted/40 p-0.5 shadow-inner">
                            <button type="button" @click="updateSpeed(1500)" :class="speed === 1500 ? 'bg-card text-foreground font-bold shadow-sm' : 'text-muted-foreground'" class="rounded-md px-2.5 py-1 transition-all">0.5x</button>
                            <button type="button" @click="updateSpeed(1000)" :class="speed === 1000 ? 'bg-card text-foreground font-bold shadow-sm' : 'text-muted-foreground'" class="rounded-md px-2.5 py-1 transition-all">1x</button>
                            <button type="button" @click="updateSpeed(500)" :class="speed === 500 ? 'bg-card text-foreground font-bold shadow-sm' : 'text-muted-foreground'" class="rounded-md px-2.5 py-1 transition-all">2x</button>
                            <button type="button" @click="updateSpeed(250)" :class="speed === 250 ? 'bg-card text-foreground font-bold shadow-sm' : 'text-muted-foreground'" class="rounded-md px-2.5 py-1 transition-all">4x</button>
                        </div>
                    </div>
                </div>

                <!-- The Interactive Board -->
                <div class="relative min-h-[350px]">
                    <template x-if="timeline.length === 0">
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-border rounded-2xl p-12 text-center bg-muted/10">
                            <svg class="w-12 h-12 text-muted-foreground/45 mb-4" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2zm0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/></svg>
                            <h5 class="text-sm font-semibold text-foreground">No Project Activity Yet</h5>
                            <p class="text-xs text-muted-foreground max-w-sm mt-1">There are no tasks or status transitions recorded for this project. Once tasks are added and moved across columns, the visual playback will unlock.</p>
                        </div>
                    </template>

                    <template x-if="timeline.length > 0">
                        <div class="grid gap-4 items-start" :style="'grid-template-columns: repeat(' + columns.length + ', minmax(0, 1fr))'">
                            <template x-for="column in columns" :key="column">
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center justify-between border-b border-border/80 pb-2 px-1">
                                        <span class="text-xs font-bold text-foreground truncate" x-text="column"></span>
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-muted text-muted-foreground" x-text="board[column] ? board[column].length : 0"></span>
                                    </div>
                                    <div class="bg-muted/10 border border-border/50 rounded-xl p-3 min-h-[300px] flex flex-col gap-2 transition-all">
                                        <template x-for="task in board[column]" :key="task.id">
                                            <div class="bg-card rounded-xl p-3 border shadow-sm transition-all duration-300 cursor-pointer hover:-translate-y-0.5 hover:shadow-md"
                                                 :class="[
                                                     isStalledAtTime(task, column) ? 'border-rose-300 dark:border-rose-900 bg-rose-500/5 dark:bg-rose-950/10 shadow-sm shadow-rose-500/5 ring-1 ring-rose-300/10' : 'border-border',
                                                     task.id === lastMovedTaskId ? 'ring-2 ring-primary ring-offset-2 dark:ring-offset-card scale-[1.02] shadow-lg border-primary/50 animate-pulse' : ''
                                                 ]"
                                                 @click="inspectingTask = task">
                                                <div class="flex justify-between items-start gap-2">
                                                    <h5 class="text-xs font-semibold text-foreground leading-tight truncate-2-lines" x-text="task.title"></h5>
                                                    <span class="text-[9px] uppercase tracking-wider font-extrabold px-1.5 py-0.5 rounded shrink-0"
                                                          :class="{
                                                              'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300': task.priority === 'High',
                                                              'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300': task.priority === 'Medium',
                                                              'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300': task.priority === 'Low',
                                                          }" x-text="task.priority"></span>
                                                </div>
                                                
                                                <template x-if="isStalledAtTime(task, column)">
                                                    <div class="mt-2 flex items-center gap-1 text-[9px] text-rose-600 dark:text-rose-400 font-extrabold uppercase tracking-wide">
                                                        <svg class="shrink-0 text-rose-500" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span>Blocked &gt; 48h</span>
                                                    </div>
                                                </template>

                                                <div class="flex justify-between items-center mt-3 pt-2 border-t border-border/40">
                                                    <div class="flex -space-x-1.5 overflow-hidden">
                                                        <template x-for="assignee in task.assignees" :key="assignee.name">
                                                            <div class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-card bg-primary/10 text-[9px] font-bold text-primary ring-1 ring-primary/20"
                                                                 :title="assignee.name" x-text="assignee.initials"></div>
                                                        </template>
                                                        <template x-if="task.assignees.length === 0">
                                                            <span class="text-[9px] text-muted-foreground/60 italic">Unassigned</span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!board[column] || board[column].length === 0">
                                            <div class="flex-1 flex items-center justify-center text-[10px] text-muted-foreground/30 italic p-4 text-center border border-dashed border-border/40 rounded-lg bg-card/10">
                                                Empty Stage
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Controls, Timeline scrubber and Details -->
                <template x-if="timeline.length > 0">
                    <div class="grid gap-6 md:grid-cols-[1fr_300px] items-stretch bg-muted/10 border border-border p-5 rounded-2xl shadow-inner">
                        <!-- Left: Slider & Controls -->
                        <div class="space-y-4 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex justify-between text-xs font-semibold text-muted-foreground">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-muted-foreground/80" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Timeline Position
                                    </span>
                                    <span class="font-bold text-foreground" x-text="(playbackIndex + 1) + ' / ' + timeline.length"></span>
                                </div>
                                <input type="range" min="0" :max="timeline.length - 1" x-model.number="playbackIndex" 
                                       @input="pause()"
                                       class="w-full h-2 bg-muted border border-border/80 rounded-lg appearance-none cursor-pointer accent-primary hover:scale-[1.005] transition-transform duration-150">
                                <div class="flex justify-between text-[10px] text-muted-foreground font-semibold">
                                    <span x-text="timeline[0] ? timeline[0].formatted_time : ''"></span>
                                    <span x-text="timeline[timeline.length - 1] ? timeline[timeline.length - 1].formatted_time : ''"></span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-border pt-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="restart()" class="inline-flex items-center justify-center p-2 rounded-lg border border-border text-foreground bg-card hover:bg-muted/50 transition-colors shadow-sm" title="Restart">
                                        <svg class="w-4 h-4" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12"/></svg>
                                    </button>
                                    <button type="button" @click="prev()" :disabled="playbackIndex === 0" class="inline-flex items-center justify-center p-2 rounded-lg border border-border text-foreground bg-card hover:bg-muted/50 transition-colors disabled:opacity-40 disabled:pointer-events-none shadow-sm" title="Previous">
                                        <svg class="w-4 h-4" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <button type="button" @click="playing ? pause() : play()" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-primary-foreground bg-primary hover:bg-primary/95 font-bold text-xs transition-colors shadow flex gap-1.5 items-center">
                                        <template x-if="playing">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4 fill-current" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px;" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                                Pause
                                            </span>
                                        </template>
                                        <template x-if="!playing">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4 fill-current" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                Play
                                            </span>
                                        </template>
                                    </button>
                                    <button type="button" @click="next()" :disabled="playbackIndex === timeline.length - 1" class="inline-flex items-center justify-center p-2 rounded-lg border border-border text-foreground bg-card hover:bg-muted/50 transition-colors disabled:opacity-40 disabled:pointer-events-none shadow-sm" title="Next">
                                        <svg class="w-4 h-4" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] uppercase font-extrabold tracking-wider px-2.5 py-0.5 rounded-full" 
                                          :class="playing ? 'bg-green-100 text-green-700 dark:bg-green-950 dark:text-green-300' : 'bg-muted text-muted-foreground'" 
                                          x-text="playing ? 'Playing' : 'Paused'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Event Log -->
                        <div class="border-t border-border pt-4 md:border-t-0 md:border-l md:pt-0 md:pl-6 flex flex-col justify-between h-full min-h-[160px]">
                            <div class="space-y-4">
                                <h5 class="text-xs font-bold text-muted-foreground uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-muted-foreground/80" style="width: 16px; height: 16px; min-width: 16px; min-height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Event Details
                                </h5>
                                <template x-if="currentEvent">
                                    <div class="bg-card border border-border rounded-xl p-4 shadow-sm space-y-3 relative overflow-hidden transition-all duration-300">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full"
                                                      :class="currentEvent.type === 'created' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'"
                                                      x-text="currentEvent.type === 'created' ? 'Created' : 'Moved'"></span>
                                                <span class="text-[10px] text-muted-foreground font-semibold" x-text="currentEvent.formatted_time"></span>
                                            </div>
                                            <button type="button" 
                                                    @click="inspectingTask = playbackTasks.find(t => t.id === currentEvent.task_id)"
                                                    class="inline-flex items-center gap-1.5 text-[9px] font-black text-primary hover:text-primary-foreground hover:bg-primary bg-primary/10 border border-primary/20 hover:border-primary px-2.5 py-1 rounded-md transition-all duration-200 shadow-sm"
                                                    title="Inspect this task's full lifecycle history">
                                                <svg class="w-3.5 h-3.5" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Inspect Task
                                            </button>
                                        </div>
                                        <p class="text-xs text-foreground font-semibold leading-relaxed" x-text="currentEvent.description"></p>
                                        <div class="flex items-center justify-between pt-2 border-t border-border/40 text-[10px] text-muted-foreground font-semibold">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-muted-foreground/60" style="width: 14px; height: 14px; min-width: 14px; min-height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span>Actor: <strong class="text-foreground" x-text="currentEvent.user"></strong></span>
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Task Inspector Modal -->
            <div x-show="inspectingTask" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-background/80 backdrop-blur-sm transition-opacity duration-300"
                 style="display: none;"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="inspectingTask = null">
                
                <div class="relative w-full max-w-lg bg-card border border-border rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300"
                     @click.outside="inspectingTask = null"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <!-- Header -->
                    <div class="flex items-start justify-between p-5 border-b border-border bg-muted/20">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] uppercase tracking-wider font-extrabold px-2 py-0.5 rounded"
                                      :class="{
                                          'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300': inspectingTask?.priority === 'High',
                                          'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300': inspectingTask?.priority === 'Medium',
                                          'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300': inspectingTask?.priority === 'Low',
                                      }" x-text="inspectingTask?.priority"></span>
                                <span class="text-xs text-muted-foreground font-semibold" x-text="'Created: ' + new Date(inspectingTask?.created_at).toLocaleDateString()"></span>
                            </div>
                            <h3 class="text-md font-bold text-foreground mt-2" x-text="inspectingTask?.title"></h3>
                        </div>
                        <button @click="inspectingTask = null" class="rounded-lg p-1.5 hover:bg-muted text-muted-foreground transition-colors">
                            <svg class="w-5 h-5" style="width: 20px; height: 20px; min-width: 20px; min-height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-5 max-h-[60vh] overflow-y-auto pr-2 scrollbar-thin">
                        <!-- Assignees -->
                        <div>
                            <h4 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Assignees</h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="assignee in inspectingTask?.assignees" :key="assignee.name">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border border-border bg-muted/40 text-xs shadow-sm">
                                        <div class="w-4 h-4 rounded-full bg-primary/15 text-primary text-[9px] font-extrabold flex items-center justify-center" x-text="assignee.initials"></div>
                                        <span class="font-semibold text-foreground" x-text="assignee.name"></span>
                                    </div>
                                </template>
                                <template x-if="!inspectingTask?.assignees || inspectingTask?.assignees.length === 0">
                                    <span class="text-xs text-muted-foreground italic">No team members assigned</span>
                                </template>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div>
                            <h4 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-3">Task Lifecycle Log</h4>
                            <div class="relative pl-5 border-l border-border/80 space-y-5">
                                <!-- Created node -->
                                <div class="relative">
                                    <div class="absolute -left-[25px] top-0.5 w-2 h-2 rounded-full border-2 border-background bg-emerald-500 shadow ring-2 ring-emerald-500/20"></div>
                                    <div class="text-xs">
                                        <div class="flex justify-between font-bold text-foreground">
                                            <span>Task Created</span>
                                            <span class="text-[10px] text-muted-foreground font-semibold" x-text="new Date(inspectingTask?.created_at).toLocaleString()"></span>
                                        </div>
                                        <div class="text-muted-foreground mt-0.5 font-medium" x-text="'Initialized in status: ' + inspectingTask?.initial_status"></div>
                                    </div>
                                </div>

                                <!-- Transitions -->
                                <template x-for="(step, idx) in inspectingTask?.history" :key="idx">
                                    <div class="relative">
                                        <div class="absolute -left-[25px] top-0.5 w-2 h-2 rounded-full border-2 border-background bg-blue-500 shadow ring-2 ring-blue-500/20"></div>
                                        <div class="text-xs">
                                            <div class="flex justify-between font-bold text-foreground">
                                                <span x-text="'Moved to ' + step.new_status"></span>
                                                <span class="text-[10px] text-muted-foreground font-semibold" x-text="step.changed_at"></span>
                                            </div>
                                            <div class="text-muted-foreground mt-0.5 flex justify-between items-center font-medium">
                                                <span x-text="'By: ' + step.changed_by"></span>
                                                <span class="text-[9px] font-bold text-blue-600 dark:text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded-full" x-show="step.duration_label" x-text="'Stayed ' + step.duration_label"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 border-t border-border bg-muted/10 flex justify-end">
                        <button @click="inspectingTask = null" class="px-4 py-2 bg-muted text-muted-foreground font-bold text-xs rounded-lg hover:bg-muted/80 transition-colors">Close Inspector</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
