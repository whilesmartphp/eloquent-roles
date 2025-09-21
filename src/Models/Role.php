<?php

namespace Whilesmart\Roles\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => false,
                'separator' => '-',
                'method'=>null,
                'maxLength'=>null,
                'maxLengthKeepWords'=>true
            ],
        ];
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RoleAssignment::class);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function grantPermission(string $permissionSlug): void
    {
        $permission = Permission::where('slug', $permissionSlug)->firstOrFail();
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    public function revokePermission(string $permissionSlug): void
    {
        $permission = Permission::where('slug', $permissionSlug)->first();
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }
}
