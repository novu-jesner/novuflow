<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth('web')->check()) {
        return redirect(auth('web')->user()->dashboardUrl());
    }
    if (auth('member')->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});
Route::middleware(['auth:web,member'])->group(function () {

 Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
    Route::get('/super-admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('dashboard.super_admin');

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/team-lead/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:team_lead')
        ->name('dashboard.team_lead');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
Route::resource('projects', ProjectController::class)->middleware('auth:web,member');
Route::resource('members', MemberController::class)->middleware('auth:web,member');

Route::middleware('auth:web,member')->group(function () {
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('/projects/{project}/columns', [ColumnController::class, 'store'])->name('columns.store');
    Route::post('/projects/{project}/columns/reorder', [ColumnController::class, 'reorder'])->name('columns.reorder');
    Route::patch('/columns/{column}', [ColumnController::class, 'update'])->name('columns.update');
    Route::delete('/columns/{column}', [ColumnController::class, 'destroy'])->name('columns.destroy');

    Route::post('/projects/{project}/members/sync', [ProjectController::class, 'syncMembers'])->name('projects.members.sync');
    Route::post('/tasks/{task}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
});

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');