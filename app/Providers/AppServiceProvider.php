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
        \Illuminate\Support\Facades\Gate::define('access-dashboard', function ($user) {
            return in_array($user->role, ['qa', 'supervisor']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-complaints', function ($user) {
            return $user->role === 'qa';
        });

        \Illuminate\Support\Facades\Gate::define('submit-complaints', function ($user) {
            return $user->role === 'qa';
        });

        \Illuminate\Support\Facades\Gate::define('approve-ncr', function ($user) {
            return $user->role === 'supervisor';
        });
    }
}
