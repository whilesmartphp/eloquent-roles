<?php

namespace Whilesmart\Roles\Commands;

use Illuminate\Console\Command;
use Whilesmart\Roles\Models\Permission;
use Whilesmart\Roles\Models\Role;

class CreatePermissionCommand extends Command
{
    protected $signature = 'permission:create
        {name : The name of the permission}
        {--description= : A description for the permission}
        {--group= : The permission group (e.g. users, admin)}
        {--assign-to= : Comma-separated role slugs to attach this permission to}';

    protected $description = 'Create a new permission and optionally assign it to roles';

    public function handle(): int
    {
        $name = $this->argument('name');
        $description = $this->option('description');
        $group = $this->option('group');
        $assignTo = $this->option('assign-to');

        $permission = Permission::firstOrCreate(
            ['name' => $name],
            [
                'description' => $description,
                'group' => $group,
            ]
        );

        if ($permission->wasRecentlyCreated) {
            $this->info("Permission '{$permission->name}' created with slug '{$permission->slug}'.");
        } else {
            $this->warn("Permission '{$permission->name}' already exists with slug '{$permission->slug}'.");
        }

        if ($assignTo) {
            $roleSlugs = array_map('trim', explode(',', $assignTo));

            foreach ($roleSlugs as $roleSlug) {
                $role = Role::where('slug', $roleSlug)->first();

                if (! $role) {
                    $this->warn("Role '{$roleSlug}' not found, skipping.");

                    continue;
                }

                $role->permissions()->syncWithoutDetaching([$permission->id]);
                $this->info("Permission '{$permission->slug}' attached to role '{$roleSlug}'.");
            }
        }

        return self::SUCCESS;
    }
}
