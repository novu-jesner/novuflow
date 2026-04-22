<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $sidebarProjects = collect();

            // Check guards independently to avoid one blocking the other if sessions overlap
            
            // 1. Web Guard (Team Lead, Admin, Super Admin)
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                $query = Project::latest();

                if ($user->role === 'team_lead') {
                    $query->where('user_id', $user->id);
                } elseif ($user->role === 'admin') {
                    $query->where('team_id', $user->team_id);
                }
                
                $sidebarProjects = $query->get();
            } 
            
            // 2. Member Guard (If web guard didn't find anything, or if they are just a member)
            // If they are logged in as both, we prioritize web guard projects for now, 
            // but we could also merge them.
            if ($sidebarProjects->isEmpty() && Auth::guard('member')->check()) {
                $sidebarProjects = Auth::guard('member')->user()->projects()->latest()->get();
            }

            $view->with('sidebarProjects', $sidebarProjects);
        });
    }
}
