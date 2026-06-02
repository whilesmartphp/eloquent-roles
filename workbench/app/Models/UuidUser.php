<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Whilesmart\Roles\Traits\HasPermissions;
use Whilesmart\Roles\Traits\HasRoles;

class UuidUser extends Authenticatable
{
    use HasFactory;
    use HasPermissions;
    use HasRoles;
    use HasUuids;

    protected $table = 'uuid_users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
