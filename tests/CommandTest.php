<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Whilesmart\Roles\Models\Role;
use Workbench\App\Models\User;

use function Orchestra\Testbench\workbench_path;

#[WithMigration]
class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_role_command()
    {
        $this->artisan('role:create', [
            'name' => 'Admin',
            '--description' => 'Administrator role',
            '--level' => 100,
        ])->assertSuccessful();

        $this->assertDatabaseHas('roles', [
            'name' => 'Admin',
            'slug' => 'admin',
            'level' => 100,
        ]);
    }

    public function test_create_role_command_warns_on_duplicate()
    {
        Role::create(['name' => 'Admin', 'level' => 100]);

        $this->artisan('role:create', ['name' => 'Admin'])
            ->expectsOutput("Role 'Admin' already exists with slug 'admin'.")
            ->assertSuccessful();
    }

    public function test_assign_role_command()
    {
        Role::create(['name' => 'Admin', 'level' => 100]);
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->artisan('role:assign', [
            'role' => 'admin',
            'model_type' => User::class,
            'model_id' => $user->id,
        ])->assertSuccessful();

        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_assign_role_command_fails_for_missing_role()
    {
        $this->artisan('role:assign', [
            'role' => 'nonexistent',
            'model_type' => User::class,
            'model_id' => 1,
        ])->assertFailed();
    }

    public function test_create_permission_command()
    {
        $this->artisan('permission:create', [
            'name' => 'Manage Users',
            '--group' => 'admin',
            '--description' => 'Can manage users',
        ])->assertSuccessful();

        $this->assertDatabaseHas('permissions', [
            'name' => 'Manage Users',
            'slug' => 'manage-users',
            'group' => 'admin',
        ]);
    }

    public function test_create_permission_with_role_assignment()
    {
        Role::create(['name' => 'Admin', 'level' => 100]);

        $this->artisan('permission:create', [
            'name' => 'Manage Users',
            '--assign-to' => 'admin',
        ])->assertSuccessful();

        $role = Role::where('slug', 'admin')->first();
        $this->assertTrue($role->hasPermission('manage-users'));
    }

    public function test_create_permission_warns_on_missing_role_for_assignment()
    {
        $this->artisan('permission:create', [
            'name' => 'Manage Users',
            '--assign-to' => 'nonexistent',
        ])
            ->expectsOutput("Role 'nonexistent' not found, skipping.")
            ->assertSuccessful();
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
