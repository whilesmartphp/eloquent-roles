<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Whilesmart\Roles\Traits\HasPermissions;
use Whilesmart\Roles\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory;
    use HasPermissions;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        if (config('roles.use_uuids', false)) {
            static::creating(function ($model) {
                if (empty($model->{$model->getKeyName()})) {
                    $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
                }
            });
        }
    }
}
