<?php

namespace App\Providers;

use App\Models\Joke;
use App\Policies\JokePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
        app('router')->aliasMiddleware('role', RoleMiddleware::class);
        app('router')->aliasMiddleware('permission', PermissionMiddleware::class);
        app('router')->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
//        Gate::policy(Joke::class, JokePolicy::class);
    }
}
