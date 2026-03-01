<?php

namespace Whilesmart\Roles\Seeders;

use Illuminate\Database\Seeder;
use Whilesmart\Roles\Models\Permission;
use Whilesmart\Roles\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles and permissions from a structured array.
     *
     * Expected format:
     * [
     *     'permissions' => [
     *         ['name' => 'Manage Users', 'group' => 'admin'],
     *         ['name' => 'Delete Users', 'group' => 'admin'],
     *     ],
     *     'roles' => [
     *         [
     *             'name' => 'Admin',
     *             'level' => 100,
     *             'permissions' => ['manage-users', 'delete-users'],
     *         ],
     *     ],
     * ]
     */
    protected array $definitions = [];

    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
    }

    protected function seedPermissions(): void
    {
        $permissions = $this->definitions['permissions'] ?? [];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                [
                    'description' => $permissionData['description'] ?? null,
                    'group' => $permissionData['group'] ?? null,
                ]
            );
        }
    }

    protected function seedRoles(): void
    {
        $roles = $this->definitions['roles'] ?? [];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'description' => $roleData['description'] ?? null,
                    'level' => $roleData['level'] ?? 0,
                ]
            );

            if (! empty($roleData['permissions'])) {
                $permissionIds = Permission::whereIn('slug', $roleData['permissions'])
                    ->pluck('id');

                $role->permissions()->syncWithoutDetaching($permissionIds);
            }
        }
    }

    /**
     * Set the definitions array programmatically.
     */
    public function withDefinitions(array $definitions): static
    {
        $this->definitions = $definitions;

        return $this;
    }
}
