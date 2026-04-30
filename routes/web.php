<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
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

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Dashboard Routes (with auth middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects
    Route::get('/dashboard/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/dashboard/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/dashboard/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/dashboard/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/dashboard/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/dashboard/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/dashboard/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

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
    Route::get('/dashboard/team', [TeamController::class, 'index'])->name('team.index');

    // Employee Tasks
    Route::get('/dashboard/my-tasks', [DashboardController::class, 'myTasks'])->name('employee.tasks');

    // Admin Routes
    Route::middleware(['role:SuperAdmin,Admin'])->prefix('dashboard/admin')->group(function () {
        Route::get('/users', [DashboardController::class, 'adminUsers'])->name('admin.users');
        Route::get('/teams', [TeamController::class, 'adminTeams'])->name('admin.teams');
        Route::get('/analytics', [DashboardController::class, 'adminAnalytics'])->name('admin.analytics');
    });
});
