<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Workspace extends Model
{
    use HasUuid;
    protected $fillable = ['name'];
}
