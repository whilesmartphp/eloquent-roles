<?php

namespace Whilesmart\Roles;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Whilesmart\Roles\Middleware\RequirePermission;
use Whilesmart\Roles\Middleware\RequireRole;
use Whilesmart\Roles\Services\PermissionService;

class RolesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PermissionService::class);
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'roles-migrations');

        // Register middleware aliases
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('role', RequireRole::class);
        $router->aliasMiddleware('permission', RequirePermission::class);
    }
}
