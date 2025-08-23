<?php

namespace Whilesmart\Roles\Contracts;

interface HasPermissions
{
    /**
     * Check if model has a specific permission in a context
     */
    public function hasPermission(string $permission, ?string $contextType = null, ?int $contextId = null): bool;

    /**
     * Check if model can perform action on subject
     */
    public function can(string $action, $subject = null, ?string $contextType = null, ?int $contextId = null): bool;

    /**
     * Grant direct permission to model
     */
    public function grantPermission(string $permission, ?string $contextType = null, ?int $contextId = null): void;

    /**
     * Revoke direct permission from model
     */
    public function revokePermission(string $permission, ?string $contextType = null, ?int $contextId = null): void;

    /**
     * Grant ability to perform action on subject
     */
    public function grantAbility(string $action, $subject = null, ?string $contextType = null, ?int $contextId = null, array $conditions = []): void;

    /**
     * Revoke ability to perform action on subject
     */
    public function revokeAbility(string $action, $subject = null, ?string $contextType = null, ?int $contextId = null): void;
}
