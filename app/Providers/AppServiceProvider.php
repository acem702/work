<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Define admin gate
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Define agent gate
        Gate::define('agent', function (User $user) {
            return $user->isAgent() || $user->isAdmin();
        });

        // Pagination
        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}