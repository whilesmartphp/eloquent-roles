<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Whilesmart\Roles\Traits\HasPermissions;
use Whilesmart\Roles\Traits\HasRoles;

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
}
