<?php

namespace Whilesmart\Roles\Services;

use Whilesmart\Roles\Models\Permission;
use Whilesmart\Roles\Models\Role;

class PermissionService
{
    public function createPermission(string $name, string $slug, ?string $description = null, ?string $group = null): Permission
    {
        return Permission::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'group' => $group,
        ]);
    }

    public function createRole(string $name, string $slug, ?string $description = null, int $level = 0): Role
    {
        return Role::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'level' => $level,
        ]);
    }

    public function setupWorkspacePermissions(): void
    {
        // Workspace permissions
        $workspacePermissions = [
            ['name' => 'View Workspace', 'slug' => 'workspace.view', 'group' => 'workspace'],
            ['name' => 'Edit Workspace', 'slug' => 'workspace.edit', 'group' => 'workspace'],
            ['name' => 'Delete Workspace', 'slug' => 'workspace.delete', 'group' => 'workspace'],
            ['name' => 'Manage Members', 'slug' => 'workspace.members.manage', 'group' => 'workspace'],
            ['name' => 'Invite Members', 'slug' => 'workspace.members.invite', 'group' => 'workspace'],
        ];

        foreach ($workspacePermissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Workspace roles
        $roles = [
            [
                'name' => 'Workspace Owner',
                'slug' => 'workspace-owner',
                'level' => 100,
                'permissions' => ['workspace.view', 'workspace.edit', 'workspace.delete', 'workspace.members.manage', 'workspace.members.invite'],
            ],
            [
                'name' => 'Workspace Admin',
                'slug' => 'workspace-admin',
                'level' => 80,
                'permissions' => ['workspace.view', 'workspace.edit', 'workspace.members.manage', 'workspace.members.invite'],
            ],
            [
                'name' => 'Workspace Member',
                'slug' => 'workspace-member',
                'level' => 20,
                'permissions' => ['workspace.view'],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'slug' => $roleData['slug'],
                    'level' => $roleData['level'],
                ]
            );

            // Attach permissions
            $permissions = Permission::whereIn('slug', $roleData['permissions'])->get();
            $role->permissions()->syncWithoutDetaching($permissions);
        }
    }

    public function setupProjectPermissions(): void
    {
        // Project permissions
        $projectPermissions = [
            ['name' => 'View Project', 'slug' => 'project.view', 'group' => 'project'],
            ['name' => 'Edit Project', 'slug' => 'project.edit', 'group' => 'project'],
            ['name' => 'Delete Project', 'slug' => 'project.delete', 'group' => 'project'],
            ['name' => 'Manage Activities', 'slug' => 'project.activities.manage', 'group' => 'project'],
        ];

        foreach ($projectPermissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }

    public function setupActivityPermissions(): void
    {
        // Activity permissions
        $activityPermissions = [
            ['name' => 'View Activities', 'slug' => 'activity.view', 'group' => 'activity'],
            ['name' => 'Create Activity', 'slug' => 'activity.create', 'group' => 'activity'],
            ['name' => 'Edit Activity', 'slug' => 'activity.edit', 'group' => 'activity'],
            ['name' => 'Delete Activity', 'slug' => 'activity.delete', 'group' => 'activity'],
        ];

        foreach ($activityPermissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
