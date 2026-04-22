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
            // Member guard: only show projects the member is explicitly assigned to
            if (auth('member')->check()) {
                $member = auth('member')->user();
                $sidebarProjects = $member->projects()->latest()->get();
                $view->with('sidebarProjects', $sidebarProjects);
                return;
            }

            // Web guard (super_admin, admin, team_lead)
            $projectsQuery = \App\Models\Project::latest();

            if (auth()->check() && auth()->user()->role === 'team_lead') {
                $projectsQuery->where('user_id', auth()->id());
            }

            $view->with('sidebarProjects', $projectsQuery->get());
        });
    }
}
