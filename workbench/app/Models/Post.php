<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Post extends Model
{
    // use HasUuids;
    protected $fillable = ['title', 'status', 'owner_id'];

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
