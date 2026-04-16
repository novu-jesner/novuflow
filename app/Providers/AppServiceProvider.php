<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $projectsQuery = \App\Models\Project::latest();
            
            if (auth()->check() && auth()->user()->role === 'team_lead') {
                $projectsQuery->where('user_id', auth()->id());
            }

            $view->with('sidebarProjects', $projectsQuery->get());
        });
    }
}
