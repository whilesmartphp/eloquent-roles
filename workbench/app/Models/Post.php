<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // use HasUuids;
    protected $fillable = ['title', 'status', 'owner_id'];
}
