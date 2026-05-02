<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Dashboard Routes (with auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Projects
    Route::get('/dashboard/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/dashboard/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/dashboard/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/dashboard/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/dashboard/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/dashboard/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/dashboard/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/dashboard/projects/{id}/members', [ProjectController::class, 'addMember'])->name('projects.members.add');
    Route::post('/dashboard/projects/{id}/members/sync', [ProjectController::class, 'syncMembers'])->name('projects.members.sync');
    Route::delete('/dashboard/projects/{id}/members/{userId}', [ProjectController::class, 'removeMember'])->name('projects.members.remove');
    Route::post('/dashboard/projects/{id}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
    Route::post('/dashboard/projects/{id}/columns', [ProjectController::class, 'addColumn'])->name('projects.columns.add');
    Route::put('/dashboard/projects/{id}/columns/{columnId}', [ProjectController::class, 'updateColumn'])->name('projects.columns.update');
    Route::delete('/dashboard/projects/{id}/columns/{columnId}', [ProjectController::class, 'deleteColumn'])->name('projects.columns.delete');
    Route::post('/dashboard/projects/{id}/columns/reorder', [ProjectController::class, 'reorderColumns'])->name('projects.columns.reorder');
    Route::get('/dashboard/projects/{id}/invitation', [ProjectController::class, 'invitation'])->name('projects.invitation');
    Route::post('/dashboard/projects/{id}/invite/accept', [ProjectController::class, 'acceptInvite'])->name('projects.invite.accept');
    Route::post('/dashboard/projects/{id}/invite/reject', [ProjectController::class, 'rejectInvite'])->name('projects.invite.reject');

    // Kanban Board
    Route::get('/dashboard/board/{boardId}', [ProjectController::class, 'board'])->name('kanban.board');

    // Tasks
    Route::post('/dashboard/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/dashboard/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/dashboard/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/dashboard/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/dashboard/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/dashboard/tasks/{id}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');

    // Team
    Route::get('/dashboard/team/{id?}', [TeamController::class, 'index'])->name('team.index');
    Route::post('/dashboard/team/invite', [TeamController::class, 'inviteMember'])->name('team.invite');
    Route::get('/dashboard/team/members/{id}/profile', [TeamController::class, 'memberProfile'])->name('team.member.profile');
    Route::post('/dashboard/team/members/{id}/tasks', [TeamController::class, 'assignTasks'])->name('team.member.tasks');
    Route::post('/dashboard/team/members/{id}/role', [TeamController::class, 'changeRole'])->name('team.member.role');
    Route::delete('/dashboard/team/members/{id}', [TeamController::class, 'removeMember'])->name('team.member.remove');

    // Employee Tasks
    Route::get('/dashboard/my-tasks', [DashboardController::class, 'myTasks'])->name('employee.tasks');

    // Admin Routes
    Route::middleware(['role:SuperAdmin,Admin'])->prefix('dashboard/admin')->group(function () {
        Route::get('/users', [DashboardController::class, 'adminUsers'])->name('admin.users');
        Route::get('/users/create', [DashboardController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::get('/users/{id}/edit', [DashboardController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/users/{id}', [DashboardController::class, 'destroyUser'])->name('admin.users.destroy');
        
        Route::get('/teams', [TeamController::class, 'adminTeams'])->name('admin.teams');
        Route::get('/teams/create', [TeamController::class, 'create'])->name('admin.teams.create');
        Route::post('/teams', [TeamController::class, 'store'])->name('admin.teams.store');
        Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('admin.teams.edit');
        Route::put('/teams/{id}', [TeamController::class, 'update'])->name('admin.teams.update');
        Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('admin.teams.destroy');
        
        Route::get('/analytics', [DashboardController::class, 'adminAnalytics'])->name('admin.analytics');
    });

    Route::post('/notifications/{id}/read', function($id) {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('notifications.read');
});
