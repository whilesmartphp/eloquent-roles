<?php

namespace Whilesmart\Roles\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface HasRoles
{
    /**
     * Get all roles for this model
     */
    public function roles(): BelongsToMany;

    /**
     * Check if model has a specific role in a context
     */
    public function hasRole(string $role, ?string $context = null, ?int $contextId = null): bool;

    /**
     * Assign a role to this model in a context
     */
    public function assignRole(string $role, ?string $context = null, ?int $contextId = null): void;

    /**
     * Remove a role from this model in a context
     */
    public function removeRole(string $role, ?string $context = null, ?int $contextId = null): void;

    /**
     * Get roles for a specific context
     */
    public function getRolesInContext(string $context, int $contextId): array;
}
