<?php

namespace Whilesmart\Roles\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ability extends Model
{
    // use HasUuids;

    protected $fillable = [
        'action',
        'subject_type',
        'subject_id',
        'assignable_type',
        'assignable_id',
        'context_type',
        'context_id',
        'allowed',
        'conditions',
    ];

    // private function boot(): uuid
    // {
    //     return $this->uuids(['id']);
    // }

    protected $casts = [
        'allowed' => 'boolean',
        'conditions' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    public function getIncrementing()
    {
        return ! config('roles.use_uuids', false);
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
