<?php

namespace Whilesmart\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Ability extends Model
{

    use HasUuids;

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

    private function boot(): uuid
    {
        return $this->uuids(['id']);
    }

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


    public $incrementing = false; //uuid aren't incrementing
    protected $keyType = 'string'; //uuid are strings
}
