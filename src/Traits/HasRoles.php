<?php

namespace Whilesmart\Roles\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\Roles\Models\Role;
use Whilesmart\Roles\Models\RoleAssignment;

trait HasRoles
{
    public function roleAssignments(): MorphMany
    {
        return $this->morphMany(RoleAssignment::class, 'assignable');
    }

    public function hasRole(string $roleSlug, ?string $contextType = null, ?int $contextId = null): bool
    {
        $query = $this->roleAssignments()
            ->whereHas('role', function ($q) use ($roleSlug) {
                $q->where('slug', $roleSlug);
            });

        if ($contextType && $contextId) {
            $query->where('context_type', $contextType)
                ->where('context_id', $contextId);
        } elseif (! $contextType && ! $contextId) {
            $query->whereNull('context_type')
                ->whereNull('context_id');
        }

        return $query->exists();
    }

    public function assignRole(string $roleSlug, ?string $contextType = null, ?int $contextId = null): void
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $this->roleAssignments()->updateOrCreate([
            'role_id' => $role->id,
            'context_type' => $contextType,
            'context_id' => $contextId,
        ]);
    }

    public function removeRole(string $roleSlug, ?string $contextType = null, ?int $contextId = null): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (! $role) {
            return;
        }

        $query = $this->roleAssignments()->where('role_id', $role->id);

        if ($contextType && $contextId) {
            $query->where('context_type', $contextType)
                ->where('context_id', $contextId);
        } elseif (! $contextType && ! $contextId) {
            $query->whereNull('context_type')
                ->whereNull('context_id');
        }

        $query->delete();
    }

    public function getRolesInContext(string $contextType, int $contextId): array
    {
        return $this->roleAssignments()
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->with('role')
            ->get()
            ->pluck('role.slug')
            ->toArray();
    }

    public function hasAnyRole(array $roles, ?string $contextType = null, ?int $contextId = null): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role, $contextType, $contextId)) {
                return true;
            }
        }

        return false;
    }
}
