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
            // For simple implementation, fetch all projects. In a real world scenario, this might be scoped by user.
            $view->with('sidebarProjects', \App\Models\Project::latest()->get());
        });
    }
}
