<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Whilesmart\Roles\Models\Permission;
use Whilesmart\Roles\Models\Role;
use Whilesmart\Roles\Seeders\RolesAndPermissionsSeeder;

use function Orchestra\Testbench\workbench_path;

#[WithMigration]
class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_permissions_and_roles()
    {
        $seeder = (new RolesAndPermissionsSeeder)->withDefinitions([
            'permissions' => [
                ['name' => 'Manage Users', 'group' => 'admin'],
                ['name' => 'Delete Users', 'group' => 'admin'],
                ['name' => 'View Dashboard', 'group' => 'admin'],
            ],
            'roles' => [
                [
                    'name' => 'Admin',
                    'level' => 100,
                    'permissions' => ['manage-users', 'delete-users', 'view-dashboard'],
                ],
                [
                    'name' => 'Moderator',
                    'level' => 50,
                    'permissions' => ['view-dashboard'],
                ],
            ],
        ]);

        $seeder->run();

        $this->assertDatabaseHas('permissions', ['slug' => 'manage-users']);
        $this->assertDatabaseHas('permissions', ['slug' => 'delete-users']);
        $this->assertDatabaseHas('permissions', ['slug' => 'view-dashboard']);
        $this->assertDatabaseHas('roles', ['slug' => 'admin', 'level' => 100]);
        $this->assertDatabaseHas('roles', ['slug' => 'moderator', 'level' => 50]);

        $adminRole = Role::where('slug', 'admin')->first();
        $this->assertEquals(3, $adminRole->permissions()->count());

        $modRole = Role::where('slug', 'moderator')->first();
        $this->assertEquals(1, $modRole->permissions()->count());
    }

    public function test_seeder_is_idempotent()
    {
        $definitions = [
            'permissions' => [
                ['name' => 'Manage Users', 'group' => 'admin'],
            ],
            'roles' => [
                [
                    'name' => 'Admin',
                    'level' => 100,
                    'permissions' => ['manage-users'],
                ],
            ],
        ];

        $seeder = (new RolesAndPermissionsSeeder)->withDefinitions($definitions);
        $seeder->run();
        $seeder->run();

        $this->assertEquals(1, Permission::count());
        $this->assertEquals(1, Role::count());
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
