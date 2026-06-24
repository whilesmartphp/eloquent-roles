<?php

namespace Whilesmart\Roles\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Whilesmart\Roles\Models\Ability;
use Whilesmart\Roles\Models\Permission;

trait HasPermissions
{
    public function abilities(): MorphMany
    {
        return $this->morphMany(Ability::class, 'assignable');
    }

    public function hasPermission(string $permissionSlug, ?string $contextType = null, ?int $contextId = null): bool
    {
        // Check direct permission grants
        $directPermission = $this->abilities()
            ->where('action', 'permission:'.$permissionSlug)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->where('allowed', true)
            ->exists();

        if ($directPermission) {
            return true;
        }

        // Check permissions through roles
        $roleAssignments = $this->roleAssignments()
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->with('role.permissions')
            ->get();

        foreach ($roleAssignments as $assignment) {
            if ($assignment->role->hasPermission($permissionSlug)) {
                return true;
            }
        }

        return false;
    }

    public function hasAbility(string $action, $subject = null, ?string $contextType = null, ?int $contextId = null): bool
    {
        $query = $this->abilities()
            ->where('action', $action)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->where('allowed', true);

        if ($subject) {
            if (is_object($subject)) {
                $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->id ?? null);
            } else {
                $query->where('subject_type', $subject);
            }
        } else {
            $query->whereNull('subject_type')
                ->whereNull('subject_id');
        }

        $ability = $query->first();

        if (! $ability) {
            return false;
        }

        // Check conditions if any
        if ($ability->conditions && $subject) {
            return $this->checkAbilityConditions($ability->conditions, $subject);
        }

        return true;
    }

    public function grantPermission(string $permissionSlug, ?string $contextType = null, ?int $contextId = null): void
    {
        $this->abilities()->updateOrCreate([
            'action' => 'permission:'.$permissionSlug,
            'context_type' => $contextType,
            'context_id' => $contextId,
        ], [
            'allowed' => true,
        ]);
    }

    public function revokePermission(string $permissionSlug, ?string $contextType = null, ?int $contextId = null): void
    {
        $this->abilities()
            ->where('action', 'permission:'.$permissionSlug)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId)
            ->delete();
    }

    public function grantAbility(string $action, $subject = null, ?string $contextType = null, ?int $contextId = null, array $conditions = []): void
    {
        $data = [
            'action' => $action,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'allowed' => true,
            'conditions' => $conditions,
        ];

        if ($subject) {
            if (is_object($subject)) {
                $data['subject_type'] = get_class($subject);
                $data['subject_id'] = $subject->id ?? null;
            } else {
                $data['subject_type'] = $subject;
            }
        }

        $this->abilities()->create($data);
    }

    public function revokeAbility(string $action, $subject = null, ?string $contextType = null, ?int $contextId = null): void
    {
        $query = $this->abilities()
            ->where('action', $action)
            ->where('context_type', $contextType)
            ->where('context_id', $contextId);

        if ($subject) {
            if (is_object($subject)) {
                $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->id ?? null);
            } else {
                $query->where('subject_type', $subject);
            }
        }

        $query->delete();
    }

    protected function checkAbilityConditions(array $conditions, $subject): bool
    {
        foreach ($conditions as $condition => $value) {
            if (! $this->evaluateCondition($condition, $value, $subject)) {
                return false;
            }
        }

        return true;
    }

    protected function evaluateCondition(string $condition, $value, $subject): bool
    {
        // Simple condition evaluation - can be extended
        switch ($condition) {
            case 'owner':
                return $subject->owner_id === $this->id;
            case 'status':
                return $subject->status === $value;
            default:
                return true;
        }
    }
}
