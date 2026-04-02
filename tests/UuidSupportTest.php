<?php
namespace Whilesmart\Roles\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Whilesmart\Roles\Models\Role;
use Workbench\App\Models\User;

use function Orchestra\Testbench\workbench_path;

#[WithMigration]
class UuidSupportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that creating a role generates or accepts UUID
     */
    public function test_role_Supports_uuid_primary_key()
    {
        //if model generates uuid automatically
        $this->artisan('role:create', [
            'name' => 'Test Role',
            '--level' => 100,
        ])->assertSuccessful();

        $role = Role::where('slug', 'test-role')->first();

        //check if the ID is a valid UUId rather than an integer
        $this->assertTrue(Str::isUuid($role->id), "The Role ID [{$role->id}] is not a valid UUID.");
        $this->assertIsString($role->id);
    }

    /**
     * Test that assigning a role works with a UUID model_id.
     */
    public function test_assign_role_command_with_uuid_model()
    {
        // 1. Create a role
        $role = Role::create(['name' => 'Editor', 'level' => 50]);
        
        // 2. Mock a user with a UUID
        $uuid = (string) Str::uuid();
        $user = User::create([
            'id' => $uuid, // Ensure your Workbench User migration supports strings
            'name' => 'UUID User',
            'email' => 'uuid@example.com',
            'password' => 'password',
        ]);

        // 3. Run the assignment command
        $this->artisan('role:assign', [
            'role' => 'editor',
            'model_type' => User::class,
            'model_id' => $uuid,
        ])->assertSuccessful();

        // 4. Verify database record in the pivot/assignment table
        $this->assertDatabaseHas('role_assignments', [
            'role_id' => $role->id,
            'assignable_id' => $uuid,
            'assignable_type' => User::class,
        ]);
    }

    /**
     * Test that permissions also support and store UUIDs.
     */
    public function test_permission_supports_uuid()
    {
        $this->artisan('permission:create', [
            'name' => 'Delete Posts',
        ])->assertSuccessful();

        $permission = \Whilesmart\Roles\Models\Permission::where('slug', 'delete-posts')->first();

        $this->assertTrue(Str::isUuid($permission->id), "The Permission ID [{$permission->id}] is not a valid UUID.");
    }

    protected function defineDatabaseMigrations(): void
    {
        // Load package migrations
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
