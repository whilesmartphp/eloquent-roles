<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Post extends Model
{
    use HasUuids;
    protected $fillable = ['title', 'status', 'owner_id'];
}
