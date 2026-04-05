<?php

namespace Whilesmart\Roles\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Faker\Factory;
use Workbench\App\Models\User;
use Workbench\App\Models\Post;
use Workbench\App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;


use function Orchestra\Testbench\workbench_path;

abstract class TestCase extends Orchestra
{

   use RefreshDatabase;
    /**
     * Set up the environment for all tests.
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('database.default', 'testing');
    }

    /**
     * Run migrations for all tests.
     */
    protected function defineDatabaseMigrations()
    {
        // 1. Pivot the default Laravel User table to UUID
        

        // 2. Load Package and Workbench migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }

    /**
     * Register Package Providers for all tests.
     */
    protected function getPackageProviders($app): array
    {
        return [
            \Whilesmart\Roles\RolesServiceProvider::class,
            \Cviebrock\EloquentSluggable\ServiceProvider::class,
        ];
    }

}