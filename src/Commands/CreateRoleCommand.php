<?php

namespace Whilesmart\Roles\Commands;

use Illuminate\Console\Command;
use Whilesmart\Roles\Models\Role;

class CreateRoleCommand extends Command
{
    protected $signature = 'role:create
        {name : The name of the role}
        {--description= : A description for the role}
        {--level=0 : The hierarchical level of the role}';

    protected $description = 'Create a new role';

    public function handle(): int
    {
        $name = $this->argument('name');
        $description = $this->option('description');
        $level = (int) $this->option('level');

        $role = Role::firstOrCreate(
            ['name' => $name],
            [
                'description' => $description,
                'level' => $level,
            ]
        );

        if ($role->wasRecentlyCreated) {
            $this->info("Role '{$role->name}' created with slug '{$role->slug}'.");
        } else {
            $this->warn("Role '{$role->name}' already exists with slug '{$role->slug}'.");
        }

        return self::SUCCESS;
    }
}
