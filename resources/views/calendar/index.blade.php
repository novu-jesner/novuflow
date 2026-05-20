@extends('layouts.dashboard')

@section('title', 'Calendar - NovuFlow')

@section('dashboard-content')
<div class="space-y-6" x-data="calendarView">
    <!-- Header Title & Filters -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-foreground">Calendar</h1>
            <p class="text-muted-foreground mt-1">Manage and reschedule task deadlines visually.</p>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div>
                <label for="team-filter" class="sr-only">Filter by Team</label>
                <select id="team-filter" x-model="selectedTeamId" class="w-full sm:w-48 px-3 py-2 bg-card border border-border rounded-xl text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-primary transition-all">
                    <option value="">All Teams</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="project-filter" class="sr-only">Filter by Project</label>
                <select id="project-filter" x-model="selectedProjectId" class="w-full sm:w-48 px-3 py-2 bg-card border border-border rounded-xl text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-primary transition-all">
                    <option value="">All Projects</option>
                    <template x-for="p in filteredProjects" :key="p.id">
                        <option :value="p.id" x-text="p.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label for="assignee-filter" class="sr-only">Filter by Assignee</label>
                <select id="assignee-filter" x-model="selectedAssigneeId" class="w-full sm:w-48 px-3 py-2 bg-card border border-border rounded-xl text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring/50 focus:border-primary transition-all">
                    <option value="">All Members</option>
                    <template x-for="u in filteredUsers" :key="u.id">
                        <option :value="u.id" x-text="u.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- Calendar Card Frame -->
    <div class="bg-card border border-border rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <!-- Calendar Navigation Header -->
        <div class="flex items-center justify-between border-b border-border px-6 py-4 bg-muted/10">
            <div class="flex items-center gap-2">
                <button @click="prevMonth()" class="p-2 hover:bg-muted rounded-xl text-muted-foreground hover:text-foreground transition-all" aria-label="Previous Month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                </button>
                <h2 class="text-xl font-bold text-foreground min-w-[150px] text-center" x-text="monthName + ' ' + currentYear"></h2>
                <button @click="nextMonth()" class="p-2 hover:bg-muted rounded-xl text-muted-foreground hover:text-foreground transition-all" aria-label="Next Month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Loading indicator -->
                <div x-show="isLoading" class="flex items-center gap-2 text-xs text-muted-foreground" x-cloak>
                    <svg class="animate-spin h-4 w-4 text-primary" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading...</span>
                </div>
                <button @click="goToToday()" class="px-4 py-2 bg-secondary text-secondary-foreground rounded-xl text-sm font-semibold hover:opacity-90 shadow-sm transition-all">
                    Today
                </button>
            </div>
        </div>

        <!-- Week Day Labels -->
        <div class="grid grid-cols-7 border-b border-border bg-muted/20 text-center font-bold text-xs uppercase tracking-wider text-muted-foreground py-3">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>

        <!-- Grid Cells -->
        <div class="grid grid-cols-7 bg-border gap-[1px]">
            <template x-for="cell in daysInGrid" :key="cell.dateString">
                <div 
                    class="min-h-[120px] bg-card p-2 flex flex-col justify-between transition-all select-none relative group"
                    :class="{ 
                        'opacity-50 bg-muted/10': !cell.isCurrentMonth,
                        'ring-2 ring-primary ring-inset z-10': cell.isToday,
                        'bg-primary/5 border-dashed border-2 border-primary': draggedOverDate === cell.dateString 
                    }"
                    @dragover.prevent
                    @dragenter="draggedOverDate = cell.dateString"
                    @dragleave="if (draggedOverDate === cell.dateString) draggedOverDate = null"
                    @drop.prevent="dropTask($event, cell.dateString)"
                >
                    <!-- Date Number -->
                    <div class="flex items-center justify-between mb-1">
                        <span 
                            class="text-sm font-semibold rounded-full w-6 h-6 flex items-center justify-center transition-all"
                            :class="{ 
                                'bg-primary text-primary-foreground font-bold shadow-sm': cell.isToday,
                                'text-foreground': cell.isCurrentMonth && !cell.isToday,
                                'text-muted-foreground/60': !cell.isCurrentMonth && !cell.isToday
                            }"
                            x-text="cell.day"
                        ></span>
                    </div>

                    <!-- Tasks List Inside Cell -->
                    <div class="flex-1 space-y-1 overflow-y-auto max-h-[85px] scrollbar-thin">
                        <template x-for="task in getTasksForDate(cell.dateString)" :key="task.id">
                            <div 
                                :draggable="task.can_edit ? 'true' : 'false'"
                                @dragstart="dragStart($event, task)"
                                @dragend="draggedOverDate = null"
                                @click="openTask(task)"
                                class="px-2 py-1 rounded text-[11px] font-medium leading-tight border transition-all cursor-pointer truncate shadow-sm flex flex-col"
                                :class="{
                                    'opacity-60 line-through border-slate-200 bg-slate-100 text-slate-400 dark:border-slate-800 dark:bg-slate-900/40': task.status === 'Completed',
                                    'border-l-4 border-red-500 bg-red-50 text-red-900 dark:bg-red-950/20 dark:text-red-300 dark:border-red-600': task.status !== 'Completed' && task.priority === 'High',
                                    'border-l-4 border-yellow-500 bg-yellow-50 text-yellow-900 dark:bg-yellow-950/20 dark:text-yellow-300 dark:border-yellow-600': task.status !== 'Completed' && task.priority === 'Medium',
                                    'border-l-4 border-slate-400 bg-slate-50 text-slate-800 dark:bg-slate-900/50 dark:text-slate-300 dark:border-slate-600': task.status !== 'Completed' && task.priority === 'Low',
                                    'cursor-grab active:cursor-grabbing hover:scale-[1.02]': task.can_edit,
                                    'cursor-pointer': !task.can_edit
                                }"
                                :title="task.title + ' (' + task.project_name + ')'"
                            >
                                <span class="font-bold truncate" x-text="task.title"></span>
                                <span class="text-[9px] opacity-75 truncate" x-text="task.project_name"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    window.calendarProjects = @json($projectsJson);
    window.calendarUsers = @json($usersJson);
</script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calendarView', () => ({
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth(),
            selectedTeamId: '',
            selectedProjectId: '',
            selectedAssigneeId: '',
            projects: window.calendarProjects || [],
            users: window.calendarUsers || [],
            tasks: [],
            isLoading: false,
            draggedTask: null,
            draggedOverDate: null,

            init() {
                this.fetchTasks();
                this.$watch('selectedTeamId', (newVal) => {
                    if (this.selectedProjectId) {
                        const exists = this.filteredProjects.some(p => p.id == this.selectedProjectId);
                        if (!exists) this.selectedProjectId = '';
                    }
                    if (this.selectedAssigneeId) {
                        const exists = this.filteredUsers.some(u => u.id == this.selectedAssigneeId);
                        if (!exists) this.selectedAssigneeId = '';
                    }
                    this.fetchTasks();
                });
                this.$watch('selectedProjectId', () => this.fetchTasks());
                this.$watch('selectedAssigneeId', () => this.fetchTasks());
            },

            get monthName() {
                const names = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                return names[this.currentMonth];
            },

            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.fetchTasks();
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.fetchTasks();
            },

            goToToday() {
                const today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                this.fetchTasks();
            },

            formatDateString(y, m, d) {
                const month = String(m).padStart(2, '0');
                const day = String(d).padStart(2, '0');
                return `${y}-${month}-${day}`;
            },

            get daysInGrid() {
                const days = [];
                const firstDayIndex = new Date(this.currentYear, this.currentMonth, 1).getDay();
                const totalDays = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const prevMonthTotalDays = new Date(this.currentYear, this.currentMonth, 0).getDate();

                // Prev Month padding days
                for (let i = firstDayIndex - 1; i >= 0; i--) {
                    const d = prevMonthTotalDays - i;
                    const m = this.currentMonth === 0 ? 11 : this.currentMonth - 1;
                    const y = this.currentMonth === 0 ? this.currentYear - 1 : this.currentYear;
                    days.push({
                        day: d,
                        month: m,
                        year: y,
                        isCurrentMonth: false,
                        isToday: this.checkIsToday(y, m, d),
                        dateString: this.formatDateString(y, m + 1, d)
                    });
                }

                // Current Month days
                for (let i = 1; i <= totalDays; i++) {
                    days.push({
                        day: i,
                        month: this.currentMonth,
                        year: this.currentYear,
                        isCurrentMonth: true,
                        isToday: this.checkIsToday(this.currentYear, this.currentMonth, i),
                        dateString: this.formatDateString(this.currentYear, this.currentMonth + 1, i)
                    });
                }

                // Next Month padding days
                const remainingCells = 42 - days.length;
                for (let i = 1; i <= remainingCells; i++) {
                    const m = this.currentMonth === 11 ? 0 : this.currentMonth + 1;
                    const y = this.currentMonth === 11 ? this.currentYear + 1 : this.currentYear;
                    days.push({
                        day: i,
                        month: m,
                        year: y,
                        isCurrentMonth: false,
                        isToday: this.checkIsToday(y, m, i),
                        dateString: this.formatDateString(y, m + 1, i)
                    });
                }

                return days;
            },

            checkIsToday(y, m, d) {
                const today = new Date();
                return today.getFullYear() === y && today.getMonth() === m && today.getDate() === d;
            },

            get filteredProjects() {
                if (!this.selectedTeamId) {
                    return this.projects;
                }
                return this.projects.filter(p => p.team_id == this.selectedTeamId);
            },

            get filteredUsers() {
                if (!this.selectedTeamId) {
                    return this.users;
                }
                return this.users.filter(u => u.team_ids.includes(Number(this.selectedTeamId)));
            },

            getTasksForDate(dateString) {
                return this.tasks.filter(t => t.due_date === dateString);
            },

            async fetchTasks() {
                this.isLoading = true;
                const gridDays = this.daysInGrid;
                if (gridDays.length === 0) return;
                const start = gridDays[0].dateString;
                const end = gridDays[gridDays.length - 1].dateString;

                let url = '/dashboard/calendar/tasks?start=' + start + '&end=' + end;
                if (this.selectedTeamId) {
                    url += '&team_id=' + this.selectedTeamId;
                }
                if (this.selectedProjectId) {
                    url += '&project_id=' + this.selectedProjectId;
                }
                if (this.selectedAssigneeId) {
                    url += '&assignee_id=' + this.selectedAssigneeId;
                }

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    this.tasks = await response.json();
                } catch (e) {
                    console.error(e);
                    if (window.Alpine && Alpine.store('toast')) {
                        Alpine.store('toast').show('Failed to load tasks.', 'error');
                    }
                } finally {
                    this.isLoading = false;
                }
            },

            dragStart(event, task) {
                if (!task.can_edit) {
                    event.preventDefault();
                    return;
                }
                this.draggedTask = task;
                event.dataTransfer.setData('text/plain', task.id);
                event.dataTransfer.effectAllowed = 'move';
            },

            async dropTask(event, targetDateString) {
                const taskId = event.dataTransfer.getData('text/plain') || (this.draggedTask ? this.draggedTask.id : null);
                if (!taskId) return;

                const task = this.tasks.find(t => t.id == taskId);
                if (!task || !task.can_edit) {
                    if (window.Alpine && Alpine.store('toast')) {
                        Alpine.store('toast').show('You do not have permission to reschedule this task.', 'error');
                    }
                    this.draggedOverDate = null;
                    return;
                }

                const originalDate = task.due_date;
                if (originalDate === targetDateString) {
                    this.draggedOverDate = null;
                    return;
                }

                // Optimistic UI Update
                task.due_date = targetDateString;
                this.draggedOverDate = null;

                try {
                    const response = await fetch('/dashboard/tasks/' + taskId + '/due-date', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            due_date: targetDateString
                        })
                    });
                    const data = await response.json();

                    if (response.ok && data.success) {
                        if (window.Alpine && Alpine.store('toast')) {
                            Alpine.store('toast').show('Rescheduled ' + task.title + ' successfully.', 'success');
                        }
                    } else {
                        throw new Error(data.message || 'Reschedule failed');
                    }
                } catch (e) {
                    console.error(e);
                    task.due_date = originalDate;
                    const errorMsg = e.message || 'Error rescheduling task.';
                    if (window.Alpine && Alpine.store('toast')) {
                        Alpine.store('toast').show(errorMsg, 'error');
                    }
                }
            },

            openTask(task) {
                window.location.href = '/dashboard/tasks/' + task.id;
            }
        }));
    });
</script>

<style>
    /* Thin scrollbar styling for calendar cells */
    .scrollbar-thin::-webkit-scrollbar {
        width: 3px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: var(--nv-border);
        border-radius: 9px;
    }
</style>
@endsection
