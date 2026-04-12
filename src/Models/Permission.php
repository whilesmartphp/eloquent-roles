<?php

namespace Whilesmart\Roles\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'group',
    ];

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // protected $keyType = 'string';
    // public $incrementing = false;
    public function getIncrementing()
    {
        return !config('roles.use_uuids', false);
    }

    public function getKeyType()
    {
        return config('roles.use_uuids', false) ? 'string' : 'int';
    }

    protected static function boot()
    {
        parent::boot();

        // Automatically handle UUID generation if configured
        if (config('roles.use_uuids', false)) {
            static::creating(function ($model) {
                if (empty($model->{$model->getKeyName()})) {
                    $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
                }
            });
        }
    }
}
