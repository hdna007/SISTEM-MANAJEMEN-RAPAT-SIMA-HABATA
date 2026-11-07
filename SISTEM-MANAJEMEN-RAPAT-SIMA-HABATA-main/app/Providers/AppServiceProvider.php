<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

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
        Carbon::setLocale('id');
        Gate::define('user-menu', function ($user) {
            return in_array($user->role, ['user','admin','proktor']);
        });

        Gate::define('admin-menu', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('sekretaris-menu', function ($user) {
            return $user->role === 'sekretaris';
        });

        Gate::define('proktor-menu', function ($user) {
            return $user->role === 'proktor';
        });

    }
}