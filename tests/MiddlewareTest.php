<?php

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Whilesmart\Roles\Middleware\RequirePermission;
use Whilesmart\Roles\Middleware\RequireRole;
use Whilesmart\Roles\Models\Permission;
use Whilesmart\Roles\Models\Role;
use Workbench\App\Models\User;

use function Orchestra\Testbench\workbench_path;

#[WithMigration]
class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'email' => Factory::create()->unique()->safeEmail,
            'name' => Factory::create()->unique()->name,
            'password' => 'password',
        ], $attributes));
    }

    protected function createRole(string $name, int $level = 0): Role
    {
        return Role::create([
            'name' => $name,
            'level' => $level,
        ]);
    }

    protected function createPermission(string $name, ?string $group = null): Permission
    {
        return Permission::create([
            'name' => $name,
            'group' => $group,
        ]);
    }

    public function test_require_role_returns_401_for_unauthenticated_user()
    {
        $request = Request::create('/test', 'GET');
        $middleware = new RequireRole;

        $response = $middleware->handle($request, fn () => new Response('OK'), 'admin');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_require_role_returns_403_when_user_lacks_role()
    {
        $user = $this->createUser();
        $this->createRole('Admin');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequireRole;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'admin');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_require_role_passes_when_user_has_role()
    {
        $user = $this->createUser();
        $this->createRole('Admin');
        $user->assignRole('admin');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequireRole;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'admin');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_require_role_passes_with_any_matching_role()
    {
        $user = $this->createUser();
        $this->createRole('Admin');
        $this->createRole('Moderator');
        $user->assignRole('moderator');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequireRole;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'admin', 'moderator');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_require_permission_returns_401_for_unauthenticated_user()
    {
        $request = Request::create('/test', 'GET');
        $middleware = new RequirePermission;

        $response = $middleware->handle($request, fn () => new Response('OK'), 'manage-users');

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_require_permission_returns_403_when_user_lacks_permission()
    {
        $user = $this->createUser();

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequirePermission;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'manage-users');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_require_permission_passes_with_direct_permission()
    {
        $user = $this->createUser();
        $user->grantPermission('manage-users');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequirePermission;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'manage-users');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_require_permission_passes_with_role_based_permission()
    {
        $user = $this->createUser();
        $role = $this->createRole('Admin', 100);
        $permission = $this->createPermission('Manage Users', 'admin');

        $role->permissions()->attach($permission);
        $user->assignRole('admin');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequirePermission;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'manage-users');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_require_permission_requires_all_permissions()
    {
        $user = $this->createUser();
        $user->grantPermission('manage-users');

        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RequirePermission;
        $response = $middleware->handle($request, fn () => new Response('OK'), 'manage-users', 'delete-users');

        $this->assertEquals(403, $response->getStatusCode());
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Whilesmart\Roles\RolesServiceProvider::class,
            \Cviebrock\EloquentSluggable\ServiceProvider::class,
        ];
    }
}
