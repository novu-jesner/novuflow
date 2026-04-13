<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ColumnController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect(auth()->user()->dashboardUrl()) 
        : redirect('/login');
});
Route::middleware(['auth'])->group(function () {

 Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

    Route::get('/super-admin/dashboard', function () {
        return view('super_admin.dashboard');
    })->middleware('role:super_admin');

   
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('role:admin');

   
    Route::get('/team-lead/dashboard', function () {
        return view('team_leader.dashboard');
    })->middleware('role:team_lead');

   
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
Route::resource('projects', ProjectController::class)->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::post('/projects/{project}/columns', [ColumnController::class, 'store'])->name('columns.store');
    Route::post('/projects/{project}/columns/reorder', [ColumnController::class, 'reorder'])->name('columns.reorder');
    Route::patch('/columns/{column}', [ColumnController::class, 'update'])->name('columns.update');
    Route::delete('/columns/{column}', [ColumnController::class, 'destroy'])->name('columns.destroy');
});

Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');