<?php

namespace Whilesmart\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ability extends Model
{
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
}
