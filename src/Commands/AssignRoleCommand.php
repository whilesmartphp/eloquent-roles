<?php

namespace Whilesmart\Roles\Commands;

use Illuminate\Console\Command;
use Whilesmart\Roles\Models\Role;
use Whilesmart\Roles\Models\RoleAssignment;

class AssignRoleCommand extends Command
{
    protected $signature = 'role:assign
        {role : The role slug to assign}
        {model_type : The fully qualified model class (e.g. App\\Models\\User)}
        {model_id : The ID of the model to assign the role to}
        {--context-type= : Optional context type for scoped assignment}
        {--context-id= : Optional context ID for scoped assignment}';

    protected $description = 'Assign a role to a model';

    public function handle(): int
    {
        $roleSlug = $this->argument('role');
        $modelType = $this->argument('model_type');
        $modelId = $this->argument('model_id');
        $contextType = $this->option('context-type');
        $contextId = $this->option('context-id') ? (int) $this->option('context-id') : null;

        $role = Role::where('slug', $roleSlug)->first();

        if (! $role) {
            $this->error("Role '{$roleSlug}' not found.");

            return self::FAILURE;
        }

        if (! class_exists($modelType)) {
            $this->error("Model class '{$modelType}' does not exist.");

            return self::FAILURE;
        }

        $model = $modelType::find($modelId);

        if (! $model) {
            $this->error("{$modelType} with ID {$modelId} not found.");

            return self::FAILURE;
        }

        RoleAssignment::updateOrCreate([
            'assignable_type' => $modelType,
            'assignable_id' => $modelId,
            'role_id' => $role->id,
            'context_type' => $contextType,
            'context_id' => $contextId,
        ]);

        $this->info("Role '{$roleSlug}' assigned to {$modelType} #{$modelId}.");

        return self::SUCCESS;
    }
}
