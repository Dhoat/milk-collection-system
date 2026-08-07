<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Define RBAC capability Gates
        Gate::define('manage-users', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->isSuperAdmin();
        });

        Gate::define('manage-villages', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'manager']);
        });

        Gate::define('view-villages', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
        });

        Gate::define('manage-farmers', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
        });

        Gate::define('manage-collections', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'manager', 'collection_staff']);
        });

        Gate::define('manage-receiving', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'manager', 'center_staff']);
        });

        Gate::define('view-financials', function (User $user) {
            return $user->hasAnyRole(['super_admin', 'manager']);
        });
    }
}
