<?php

namespace Whilesmart\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class RoleAssignment extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'assignable_type',
        'assignable_id',
        'role_id',
        'context_type',
        'context_id',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function context(): MorphTo
    {
        return $this->morphTo();
    }

    protected $keyType = 'string';
    public $incrementing = false;
}
