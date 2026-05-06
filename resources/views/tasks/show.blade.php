@extends('layouts.dashboard')

@section('dashboard-content')
<div class="max-w-3xl mx-auto">
    <style>
        .comment-body {
            word-break: break-all !important;
            overflow-wrap: anywhere !important;
            white-space: pre-wrap !important;
            max-width: 100% !important;
            display: block !important;
        }
        .flex-container-safe {
            min-width: 0 !important;
            flex: 1 1 0% !important;
        }
    </style>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900">Task Details</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex items-start justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">{{ $task->title }}</h2>
                <span class="px-3 py-1 text-sm rounded-full
                    @if($task->priority == 'High') bg-orange-100 text-orange-700
                    @elseif($task->priority == 'Medium') bg-yellow-100 text-yellow-700
                    @else bg-gray-100 text-gray-700 @endif">{{ $task->priority }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 text-sm rounded-full
                    @if($task->status == 'To Do') bg-gray-100 text-gray-700
                    @elseif($task->status == 'In Progress') bg-blue-100 text-blue-700
                    @elseif($task->status == 'Review') bg-yellow-100 text-yellow-700
                    @elseif($task->status == 'Completed') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700 @endif">{{ $task->status }}</span>
                <span class="text-sm text-gray-500">in</span>
                <a href="{{ route('projects.show', $task->project_id) }}" class="text-sm text-[#3f8caf] hover:underline">{{ $task->project->name ?? 'Unknown Project' }}</a>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Description -->
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                <p class="text-gray-600">{{ $task->description ?? 'No description provided' }}</p>
            </div>

            <!-- Details Grid -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Assigned To</h3>
                    <div class="flex items-center gap-2">
                        @if($task->assignee)
                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm">{{ substr($task->assignee->name, 0, 1) }}</div>
                        <span class="text-gray-600">{{ $task->assignee->name }}</span>
                        @else
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white text-sm">-</div>
                        <span class="text-gray-500">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Created By</h3>
                    <div class="flex items-center gap-2">
                        @if($task->creator)
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-sm">{{ substr($task->creator->name, 0, 1) }}</div>
                        <span class="text-gray-600">{{ $task->creator->name }}</span>
                        @else
                        <span class="text-gray-500">Unknown</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Due Date</h3>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                        </svg>
                        {{ $task->due_date ? $task->due_date->format('F d, Y') : 'No due date' }}
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Created On</h3>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        {{ $task->created_at->format('F d, Y') }}
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            @if($task->members && $task->members->count() > 0)
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Team Members</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($task->members as $member)
                    <div class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg">
                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs">{{ substr($member->name, 0, 1) }}</div>
                        <span class="text-sm text-gray-600">{{ $member->name }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="p-6 border-t bg-gray-50 flex items-center justify-between">
            <span class="text-sm text-gray-500">Last updated: {{ $task->updated_at->diffForHumans() }}</span>
            <div class="flex gap-3">
                <a href="{{ route('kanban.board', $task->project_id) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-white transition-colors">
                    Back to Board
                </a>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="mt-6 bg-white rounded-lg shadow" x-data="{
        comments: {{ $task->comments->whereNull('parent_id')->map(fn($c) => [
            'id'         => $c->id,
            'body'       => $c->body,
            'created_at' => $c->created_at->diffForHumans(),
            'user'       => ['name' => $c->user->name, 'initials' => strtoupper(substr($c->user->name, 0, 1))],
            'attachments' => $c->attachments->map(fn($a) => [
                'id' => $a->id,
                'name' => $a->file_name,
                'url' => $a->url,
                'is_image' => $a->isImage(),
            ]),
            'replies' => $c->replies->map(fn($r) => [
                'id'         => $r->id,
                'parent_id'  => $r->parent_id,
                'reply_to_id' => $r->reply_to_id,
                'reply_to'   => $r->replyTo ? [
                    'user' => [
                        'name' => $r->replyTo->user->name,
                        'initials' => strtoupper(substr($r->replyTo->user->name, 0, 1)),
                    ],
                    'body' => $r->replyTo->body,
                ] : null,
                'body'       => $r->body,
                'created_at' => $r->created_at->diffForHumans(),
                'user'       => ['name' => $r->user->name, 'initials' => strtoupper(substr($r->user->name, 0, 1))],
                'attachments' => $r->attachments->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->file_name,
                    'url' => $a->url,
                    'is_image' => $a->isImage(),
                ]),
                'can_delete' => auth()->id() === $r->user_id,
                'can_edit'   => auth()->id() === $r->user_id,
                'is_edited'  => $r->updated_at->gt($r->created_at),
            ]),
            'can_delete' => auth()->id() === $c->user_id,
            'can_edit'   => auth()->id() === $c->user_id,
            'is_edited'  => $c->updated_at->gt($c->created_at),
        ])->values()->toJson() }},
        newComment: '',
        replyingTo: null,
        selectedFiles: [],
        isSubmitting: false,
        editingComment: null,
        editBody: '',
        showAllComments: false,
        commentLimit: 5,
        init() {
            this.$nextTick(() => {
                if (window.location.hash) {
                    const el = document.querySelector(window.location.hash);
                    if (el) {
                        setTimeout(() => {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 500);
                    }
                }
            });
        },
        handleFileSelect(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (file.size > 10 * 1024 * 1024) {
                    $store.toast.show('File ' + file.name + ' is too large (max 10MB)', 'error');
                    return;
                }
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.selectedFiles.push({
                        file: file,
                        name: file.name,
                        preview: file.type.startsWith('image/') ? event.target.result : null,
                        isImage: file.type.startsWith('image/')
                    });
                };
                reader.readAsDataURL(file);
            });
            e.target.value = '';
        },
        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },
        async submitComment() {
            if ((!this.newComment.trim() && this.selectedFiles.length === 0) || this.isSubmitting) return;
            this.isSubmitting = true;
            
            const formData = new FormData();
            formData.append('body', this.newComment);
            if (this.replyingTo) {
                // The parent_id is the root comment ID (top-level threading)
                const parentId = this.replyingTo.parent_id || this.replyingTo.id;
                formData.append('parent_id', parentId);
                // The reply_to_id is the specific message being clicked
                formData.append('reply_to_id', this.replyingTo.id);
            }
            this.selectedFiles.forEach(f => {
                formData.append('attachments[]', f.file);
            });

            try {
                const res = await fetch('{{ route('tasks.comments.store', $task->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    if (data.comment.parent_id) {
                        const parent = this.comments.find(c => c.id == data.comment.parent_id);
                        if (parent) {
                            if (!parent.replies) parent.replies = [];
                            parent.replies.push(data.comment);
                        }
                    } else {
                        this.comments.unshift(data.comment);
                    }
                    this.newComment = '';
                    this.replyingTo = null;
                    this.selectedFiles = [];
                    $store.toast.show('Comment posted', 'success');
                } else {
                    $store.toast.show(data.message || 'Failed to post', 'error');
                }
            } catch(e) {
                $store.toast.show('Network error', 'error');
            }
        },
        startEdit(comment) {
            this.editingComment = comment;
            this.editBody = comment.body;
            this.$nextTick(() => {
                const textarea = document.getElementById('edit-textarea-' + comment.id);
                if (textarea) textarea.focus();
            });
        },
        cancelEdit() {
            this.editingComment = null;
            this.editBody = '';
        },
        async updateComment() {
            if (!this.editBody.trim() || !this.editingComment) return;
            const commentId = this.editingComment.id;
            try {
                const res = await fetch('/dashboard/tasks/{{ $task->id }}/comments/' + commentId, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ body: this.editBody })
                });
                const data = await res.json();
                if (data.success) {
                    this.editingComment.body = data.comment.body;
                    this.editingComment.is_edited = true;
                    this.cancelEdit();
                    $store.toast.show('Comment updated', 'success');
                } else {
                    $store.toast.show(data.message || 'Update failed', 'error');
                }
            } catch(e) {
                $store.toast.show('Network error', 'error');
            }
        },
        async deleteComment(commentId) {
            if (!confirm('Delete this comment?')) return;
            try {
                const res = await fetch('/dashboard/tasks/{{ $task->id }}/comments/' + commentId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    }
                });
                if (res.ok) {
                    // Remove from top-level or from any replies array
                    const isTopLevel = this.comments.some(c => c.id === commentId);
                    if (isTopLevel) {
                        this.comments = this.comments.filter(c => c.id !== commentId);
                    } else {
                        this.comments.forEach(c => {
                            if (c.replies) {
                                c.replies = c.replies.filter(r => r.id !== commentId);
                            }
                        });
                    }
                    $store.toast.show('Comment deleted', 'success');
                }
            } catch(e) {
                $store.toast.show('Network error', 'error');
            }
        },
        async deleteAttachment(comment, attachmentId) {
            if (!confirm('Delete this attachment?')) return;
            try {
                const res = await fetch('/dashboard/tasks/comments/attachments/' + attachmentId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    }
                });
                if (res.ok) {
                    comment.attachments = comment.attachments.filter(a => a.id !== attachmentId);
                    $store.toast.show('Attachment deleted', 'success');
                }
            } catch(e) {
                $store.toast.show('Network error', 'error');
            }
        }
    }">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Comments
                <span class="text-sm font-normal text-gray-400" x-text="'(' + comments.length + ')'"></span>
            </h3>
        </div>

        <div class="p-6 space-y-4">
            <!-- Comment Form -->
            @if($canComment)
            <div class="flex gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center text-white text-sm font-semibold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div x-show="replyingTo" class="mb-2 flex items-center justify-between bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                        <div class="flex-1 min-w-0 pr-4">
                            <span class="text-xs text-blue-700">
                                Replying to <span class="font-semibold" x-text="replyingTo.user.name"></span>
                            </span>
                            <p class="text-[10px] text-blue-500 truncate mt-0.5 opacity-80" x-text="replyingTo.body || (replyingTo.attachments && replyingTo.attachments.length > 0 ? (replyingTo.attachments[0].is_image ? '[Image]' : '[Attachment]') : '[Message]')"></p>
                        </div>
                        <button @click="replyingTo = null" class="text-blue-400 hover:text-blue-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="relative">
                        <textarea
                            x-model="newComment"
                            @keydown.ctrl.enter="submitComment()"
                            rows="3"
                            :placeholder="replyingTo ? 'Write a reply...' : 'Write a comment… (Ctrl+Enter to submit)'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#54acc8] focus:border-transparent resize-none"
                        ></textarea>
                        
                        <!-- Selected Files Preview -->
                        <div x-show="selectedFiles.length > 0" class="mt-2 flex flex-wrap gap-2">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="relative group">
                                    <template x-if="file.isImage">
                                        <img :src="file.preview" class="w-16 h-16 object-cover rounded-md border shadow-sm">
                                    </template>
                                    <template x-if="!file.isImage">
                                        <div class="w-16 h-16 bg-gray-50 border rounded-md flex flex-col items-center justify-center p-1 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                            <span class="text-[10px] text-gray-500 truncate w-full px-1" x-text="file.name"></span>
                                        </div>
                                    </template>
                                    <button @click="removeFile(index)" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2">
                            <input type="file" x-ref="fileInput" @change="handleFileSelect" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                            <button @click="$refs.fileInput.click()" class="p-2 text-gray-500 hover:text-[#3f8caf] hover:bg-gray-100 rounded-md transition-colors" title="Attach files">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.51a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                </svg>
                            </button>
                            <button @click="$refs.fileInput.click()" class="p-2 text-gray-500 hover:text-[#3f8caf] hover:bg-gray-100 rounded-md transition-colors" title="Attach photos">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                            </button>
                        </div>
                        <button
                            @click="submitComment()"
                            :disabled="(!newComment.trim() && selectedFiles.length === 0) || isSubmitting"
                            :class="{ 'opacity-50 cursor-not-allowed': (!newComment.trim() && selectedFiles.length === 0) || isSubmitting }"
                            class="px-4 py-2 bg-gradient-to-r from-[#3f8caf] to-[#54acc8] text-white text-sm font-medium rounded-md hover:from-[#2a6a95] hover:to-[#3f8caf] transition-colors">
                            <span x-show="!isSubmitting">Post Comment</span>
                            <span x-show="isSubmitting">Posting…</span>
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="text-sm text-gray-500 bg-gray-50 rounded-lg px-4 py-3 border border-gray-200">
                Only the task assignee and team leaders can comment on this task.
            </div>
            @endif

            <!-- Divider -->
            <template x-if="comments.length > 0">
                <hr class="border-gray-100">
            </template>

            <!-- Comments List -->
            <template x-if="comments.length === 0">
                <div class="text-center py-8 text-gray-400 text-sm">
                    No comments yet.
                </div>
            </template>

            <div class="divide-y divide-gray-100">
                <template x-for="(comment, index) in (showAllComments ? comments : comments.slice(0, commentLimit))" :key="comment.id">
                    <div class="py-6 first:pt-0">
                    <div :id="'comment-' + comment.id" class="flex gap-3 group target:bg-blue-50 target:ring-2 target:ring-blue-100 target:rounded-xl p-2 transition-all duration-500 w-full min-w-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#3f8caf] to-[#54acc8] flex items-center justify-center text-white text-base font-bold shrink-0 shadow-sm"
                             x-text="comment.user.initials"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900" x-text="comment.user.name"></span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-[11px] text-gray-400 font-medium" x-text="comment.created_at"></span>
                                        <template x-if="comment.is_edited">
                                            <span class="text-[10px] text-gray-300 font-normal">(edited)</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button
                                        @click="replyingTo = comment; $nextTick(() => document.querySelector('textarea').focus())"
                                        class="flex items-center gap-1 px-2 py-1 rounded hover:bg-blue-50 text-gray-400 hover:text-[#3f8caf] transition-colors"
                                        title="Reply">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
                                        </svg>
                                        <span class="text-xs font-medium">Reply</span>
                                    </button>
                                    <div class="flex items-center gap-1">
                                        <button
                                            x-show="comment.can_edit"
                                            @click="startEdit(comment)"
                                            class="p-1 rounded hover:bg-blue-50 text-gray-300 hover:text-blue-500 transition-colors"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </button>
                                        <button
                                            x-show="comment.can_delete"
                                            @click="deleteComment(comment.id)"
                                            class="p-1 rounded hover:bg-red-50 text-gray-300 hover:text-red-500 transition-colors"
                                            title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="block w-full min-w-0" x-show="comment.body && comment.body.trim().length > 0 && editingComment?.id !== comment.id">
                                <p class="comment-body text-[15px] text-gray-800 leading-relaxed mb-1" x-text="comment.body"></p>
                            </div>

                            <!-- Edit Mode for Comment -->
                            <div x-show="editingComment?.id === comment.id" class="mt-2">
                                <textarea 
                                    :id="'edit-textarea-' + comment.id"
                                    x-model="editBody"
                                    class="w-full p-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white"
                                    rows="3"
                                ></textarea>
                                <div class="flex justify-end gap-2 mt-2">
                                    <button @click="cancelEdit()" class="px-3 py-1 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                    <button @click="updateComment()" class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Save Changes</button>
                                </div>
                            </div>
                            
                            <!-- Attachments Display -->
                            <div x-show="comment.attachments.length > 0" class="mt-2 flex flex-wrap gap-2">
                                <template x-for="file in comment.attachments" :key="file.id">
                                    <div class="relative group/file">
                                        <a :href="file.url" target="_blank" class="block">
                                            <template x-if="file.is_image">
                                                <img :src="file.url" class="w-24 h-24 object-cover rounded-lg border hover:opacity-90 transition-opacity shadow-sm">
                                            </template>
                                            <template x-if="!file.is_image">
                                                <div class="flex items-center gap-2 px-3 py-2 bg-white border rounded-lg hover:border-[#3f8caf] transition-colors shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                    <span class="text-xs text-gray-600 truncate max-w-[120px]" x-text="file.name"></span>
                                                </div>
                                            </template>
                                        </a>
                                        <button 
                                            x-show="comment.can_delete"
                                            @click="deleteAttachment(comment, file.id)"
                                            class="absolute -top-1.5 -right-1.5 bg-white border shadow-md rounded-full p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover/file:opacity-100 transition-opacity">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Replies List (Discord Style) -->
                    <div x-show="comment.replies && comment.replies.length > 0" class="mt-3 space-y-2 ml-4 border-l-2 border-gray-100 pl-4">
                        <template x-for="reply in comment.replies" :key="reply.id">
                            <div class="relative group/reply w-full min-w-0">
                                <div :id="'comment-' + reply.id" class="flex flex-col gap-1 p-2 rounded-xl hover:bg-gray-50/80 transition-colors target:bg-blue-50 w-full min-w-0">
                                    <!-- Referenced Message Snippet -->
                                    <template x-if="reply.reply_to">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-0.5 ml-2 overflow-hidden">
                                            <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] shrink-0" x-text="reply.reply_to.user.initials"></div>
                                            <span class="font-medium text-gray-400 shrink-0" x-text="'@' + reply.reply_to.user.name"></span>
                                            <span class="truncate opacity-60 min-w-0" x-text="(reply.reply_to.body || '[Attachment]').substring(0, 40) + (reply.reply_to.body && reply.reply_to.body.length > 40 ? '...' : '')"></span>
                                        </div>
                                    </template>
                                    <template x-if="!reply.reply_to">
                                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-0.5 ml-2 overflow-hidden">
                                            <div class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center text-[10px] shrink-0" x-text="comment.user.initials"></div>
                                            <span class="font-medium text-gray-400 shrink-0" x-text="'@' + comment.user.name"></span>
                                            <span class="truncate opacity-60 min-w-0" x-text="(comment.body || '[Attachment]').substring(0, 40) + (comment.body && comment.body.length > 40 ? '...' : '')"></span>
                                        </div>
                                    </template>

                                    <div class="flex gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-[10px] font-bold shrink-0 border border-gray-200"
                                             x-text="reply.user.initials"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-[13px] font-bold text-gray-800" x-text="reply.user.name"></span>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] text-gray-400 font-medium" x-text="reply.created_at"></span>
                                                        <template x-if="reply.is_edited">
                                                            <span class="text-[10px] text-gray-300 font-normal">(edited)</span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <button
                                                        @click="replyingTo = reply; $nextTick(() => document.querySelector('textarea').focus())"
                                                        class="flex items-center gap-1 px-2 py-1 rounded hover:bg-blue-50 text-gray-400 hover:text-[#3f8caf] transition-colors"
                                                        title="Reply">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
                                                        </svg>
                                                        <span class="text-xs font-medium">Reply</span>
                                                    </button>
                                                    <div class="flex items-center gap-1">
                                                        <button
                                                            x-show="reply.can_edit"
                                                            @click="startEdit(reply)"
                                                            class="p-1 rounded hover:bg-blue-50 text-gray-300 hover:text-blue-500 transition-colors"
                                                            title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                        </button>
                                                        <button
                                                            x-show="reply.can_delete"
                                                            @click="deleteComment(reply.id)"
                                                            class="p-1 rounded hover:bg-red-50 text-gray-300 hover:text-red-500 transition-colors"
                                                            title="Delete">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="block w-full min-w-0" x-show="reply.body && reply.body.trim().length > 0 && editingComment?.id !== reply.id">
                                                <p class="comment-body text-[13px] text-gray-700 bg-gray-100/60 rounded-lg px-3 py-2 border border-gray-100/50" x-text="reply.body"></p>
                                            </div>

                                            <!-- Edit Mode for Reply -->
                                            <div x-show="editingComment?.id === reply.id" class="mt-2">
                                                <textarea 
                                                    :id="'edit-textarea-' + reply.id"
                                                    x-model="editBody"
                                                    class="w-full p-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white"
                                                    rows="2"
                                                ></textarea>
                                                <div class="flex justify-end gap-2 mt-1.5">
                                                    <button @click="cancelEdit()" class="px-2 py-0.5 text-xs text-gray-500 hover:text-gray-700">Cancel</button>
                                                    <button @click="updateComment()" class="px-2 py-0.5 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Save</button>
                                                </div>
                                            </div>
                                            
                                            <!-- Attachments Display -->
                                            <div x-show="reply.attachments.length > 0" class="mt-2 flex flex-wrap gap-2">
                                                <template x-for="file in reply.attachments" :key="file.id">
                                                    <div class="relative group/file">
                                                        <a :href="file.url" target="_blank" class="block">
                                                            <template x-if="file.is_image">
                                                                <img :src="file.url" class="w-20 h-20 object-cover rounded-lg border hover:opacity-90 transition-opacity shadow-sm">
                                                            </template>
                                                            <template x-if="!file.is_image">
                                                                <div class="flex items-center gap-2 px-3 py-2 bg-white border rounded-lg hover:border-[#3f8caf] transition-colors shadow-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                                    <span class="text-xs text-gray-600 truncate max-w-[100px]" x-text="file.name"></span>
                                                                </div>
                                                            </template>
                                                        </a>
                                                        <button 
                                                            x-show="reply.can_delete"
                                                            @click="deleteAttachment(reply, file.id)"
                                                            class="absolute -top-1.5 -right-1.5 bg-white border shadow-md rounded-full p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover/file:opacity-100 transition-opacity">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- See More Button -->
            <template x-if="!showAllComments && comments.length > commentLimit">
                <div class="mt-4 text-center">
                    <button @click="showAllComments = true" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-sm font-medium rounded-full border border-gray-200 transition-all hover:shadow-sm group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-y-0.5 transition-transform">
                            <path d="m7 13 5 5 5-5M7 6l5 5 5-5"/>
                        </svg>
                        <span>View <span x-text="comments.length - commentLimit"></span> earlier comments</span>
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
