<?php

namespace Whilesmart\Roles\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
// use Orchestra\Testbench\TestCase;
use Whilesmart\Roles\Models\Role;
use Workbench\App\Models\UuidUser;

#[WithMigration]
class UuidSupportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Define environment setup.
     */
    protected function defineEnvironment($app)
    {
        // FORCE UUID support for this specific test file
        $app['config']->set('roles.use_uuids', true);
    }

    /**
     * Test that assigning a role works with a UUID model_id.
     */
    public function test_assign_role_command_with_uuid_model()
    {
        // 1. Create a role
        $role = Role::create(['name' => 'Editor', 'level' => 50]);

        // 2. Mock a user with a UUID
        $user = UuidUser::create([
            'name' => 'UUID User',
            'email' => 'uuid@example.com',
            'password' => 'password',
        ]);

        $uuid = $user->id; // This should be the UUID

        // 3. Run the assignment command
        $this->artisan('role:assign', [
            'role' => 'editor',
            'model_type' => UuidUser::class,
            'model_id' => $uuid,
        ])->assertSuccessful();

        // 4. Verify database record in the pivot/assignment table
        $this->assertDatabaseHas('role_assignments', [
            'role_id' => $role->id,
            'assignable_id' => $uuid,
            'assignable_type' => UuidUser::class,
        ]);
    }
}
