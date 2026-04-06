<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;    
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
        return view('team_lead.dashboard');
    })->middleware('role:team_lead');

   
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');