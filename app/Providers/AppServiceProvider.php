<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Gunakan template pagination bertema DataTables SB Admin 2
        Paginator::defaultView('vendor.pagination.sb-admin-2');
        Paginator::defaultSimpleView('vendor.pagination.sb-admin-2');

        // Admin sengaja diarahkan ke dashboard supervisor (lihat DashboardController::index),
        // jadi admin harus bisa mengakses menu analitik yang sama: seven-tools, apriori,
        // customers, laporan, visit, dan defect-dictionary.
        \Illuminate\Support\Facades\Gate::define('access-dashboard', function ($user) {
            return in_array($user->role, ['qa', 'supervisor', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-complaints', function ($user) {
            return $user->role === 'qa';
        });

        \Illuminate\Support\Facades\Gate::define('submit-complaints', function ($user) {
            return $user->role === 'qa';
        });

        // Supervisor dan admin sama-sama menangani persetujuan NCR & jadwal visit
        // (dashboard supervisor menampilkan kedua aksi ini untuk kedua role).
        \Illuminate\Support\Facades\Gate::define('approve-ncr', function ($user) {
            return in_array($user->role, ['supervisor', 'admin']);
        });
    }
}
