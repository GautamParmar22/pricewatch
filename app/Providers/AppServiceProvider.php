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
        // GP - 19-08-2026 code comment - Register Workspace and Competitor policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Workspace::class, \App\Policies\WorkspacePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Competitor::class, \App\Policies\CompetitorPolicy::class);
    }
}
